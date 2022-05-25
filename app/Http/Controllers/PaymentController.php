<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;
use PayPalHttp\HttpResponse;
use PayPalHttp\HttpException;
use App\Events\NewTournamentEvent;
use Illuminate\Support\Facades\App;
use App\Events\ScheduleEnrolledEvent;
use App\Models\Room;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use App\Notifications\ScheduleEnrolledNotification;

class PaymentController extends Controller
{
   public function create(Request $request ,Schedule $Schedule){


    $request = new OrdersCreateRequest();
    $request->prefer('return=representation');
    $request->body = [
                         "intent" => "CAPTURE",
                         "purchase_units" => [[
                             "reference_id" =>$Schedule->id,
                             "amount" => [
                                 "value" => $Schedule->price,
                                 "currency_code" => "ILS",
                             ]
                         ]],
                         "application_context" =>
                            array(
                                "locale" => "en-US",
                                "cancel_url" => "http://localhost:3000/Courses",
                                "return_url" => "http://localhost:3000/return/".$Schedule->id."/",
                            ),


                     ];

    try {
        // Call API with your client and get a response for your call
        $client=App::make('paypal.client');
        $response = $client->execute($request);

        if($response->statusCode==201){

            foreach($response->result->links as $link){
                if($link->rel=='approve'){
                    return $link->href;
                }
            }
        }
        // If call returns body in response, you can get the deserialized version from the result attribute of the response
    }catch (HttpException $ex) {
        echo $ex->statusCode;
        print_r($ex->getMessage());
    }
   }

   public function callback(Request $request,Schedule $Schedule){

    $paypalScheduleId=$request->query('token');
    $captureRequest = new OrdersCaptureRequest($paypalScheduleId);
    $captureRequest->prefer('return=representation');
    try {
    // Call API with your client and get a response for your call
    $client=App::make('paypal.client');

    $response = $client->execute($captureRequest);
    if($response->statusCode==201 && $response->result->status=='COMPLETED'){

      $Schedule->user()->associate($request->user());
      $Schedule->save();
      $Schedule->trainer->notify(new ScheduleEnrolledNotification($Schedule));
      event(new NewTournamentEvent('New student has enrolled to your session',$Schedule->trainer->username));
      $flag=true;
      if(Room::where('FirstUserUsername','=',$request->user()->username)->where('SecondUserUsername','=',$Schedule->trainer->username)->first())
         $flag=false;
      if(Room::where('SecondUserUsername','=',$request->user()->username)->where('FirstUserUsername','=',$Schedule->trainer->username)->first())
        $flag=false;
      if($flag)
       {
            Room::create([
        'FirstUserUsername'=>$request->user()->username,
        'SecondUserUsername'=>$Schedule->trainer->username,

    ]);
}
}



    }
    catch (HttpException $ex) {
    echo $ex->statusCode;
    print_r($ex->getMessage());
    }

   }
}
