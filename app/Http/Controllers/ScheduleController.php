<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Price;
use App\Models\Trainer;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        return Schedule::all();
       // return Trainer::whereid($request->user()->id)->first()->schedules;
    }

    public function getUC(Request $request)
    {
        //

      return User::whereid($request->user()->id)->first()->schedules()->with('trainer')->get();

    }
    public function getAllTC(Trainer $trainer)
    {

      return $trainer->schedules()->with('user')->get();

    }

    public function getTC(Trainer $trainer)
    {
        //
        $now=date('Y-m-d H:i:s');
       $end= date('Y-m-d H:i:s', strtotime($now. ' + 7 days'));
      return $trainer->schedules()->where('startDate','>',$now)->where('endDate','<', $end)->where('user_id','=',NULL)->get();

    }

    public function getAllTCmy(Request $request)
    {

      return $request->user()->schedules()->with('user')->get();

    }

    public function coursesCount(Request $request)
    {
        $data=[];
        $now=date('Y-m-d');

        $plus=0;


       for($i=11;$i>=0;$i--){
        $end= date('Y-m-d', strtotime($now. '  - '.$plus.' months'));
        $plus++;
        $from=date('Y-m-d', strtotime(date('Y-m', strtotime($end))));
        $to=date('Y-m-d', strtotime(date('Y-m', strtotime($end)). '  + 30 day'));
        $endd=strtotime($end);

        $data[$i]=[
              'name'=>date("M", $endd),
             'Reserved'=>Schedule::whereBetween('startDate', [$from,$to])->where('user_id','!=',null)->get()->count(),
             'Free'=>Schedule::whereBetween('startDate', [$from,$to])->where('user_id','=',null)->get()->count(),
        ];


       }

         return $data;


    }
    public function incomeSum(Request $request)
    {
        $data=[];
        $now=date('Y-m-d');

        $plus=0;


       for($i=11;$i>=0;$i--){
        $end= date('Y-m-d', strtotime($now. '  - '.$plus.' months'));
        $plus++;
        $from=date('Y-m-d', strtotime(date('Y-m', strtotime($end))));
        $to=date('Y-m-d', strtotime(date('Y-m', strtotime($end)). '  + 30 day'));
        $endd=strtotime($end);

        $data[$i]=[
              'name'=>date("M", $endd),
             'income'=>Schedule::whereBetween('startDate', [$from,$to])->where('user_id','!=',null)->get()->sum('price'),
        ];


       }

         return $data;


    }
    public function getRTC(Request $request)
    {
        //

        $now=date('Y-m-d H:i:s');

        $before= date('Y-m-d H:i:s', strtotime($now. ' - 1 days'));
       $end= date('Y-m-d H:i:s', strtotime($now. ' + 7 days'));
      return $request->user()->schedules()->with('user')->where('startDate','>',$before)->where('endDate','<', $end)->where('user_id','!=',NULL)->orderBy('updated_at', 'DESC')->get();

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'startDate' => ['required','date','after:today'],
            'endDate'=>['required','date','after:today'],
         ]);
         $start=date('Y-m-d H:i:s', strtotime($request->post('startDate')));
         $end=date('Y-m-d H:i:s', strtotime($request->post('endDate')));
         $mins= strtotime($end) - strtotime($start);
         $pricePerMin=Price::all()->first()['Price_Per_Min'];
         $price= ($mins/60)*$pricePerMin;

            $s=Schedule::create([
             'title'=>$request->user()->name.'('.$request->user()->email.')-'.$request->post('title'),
             'location'=>$request->post('location'),
             'notes'=>$request->post('notes'),
             'price'=>$price,
             'startDate'=> $start,
             'endDate'=>$end,
             'allDay'=>$request->post('allDay'),
            ]);

                $trainer=Trainer::findOrFail($request->user()->id);
                $s->trainer()->associate($trainer);
                $s->save();

        return  $s;

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Schedule $schedule)
    {

         if($request->user()->email!=$schedule->trainer->email)
        return response(["errors" => ["trainer"=>"that's another trainer course"]], 400);

        if($schedule->startDate<date('Y-m-d H:i:s'))
        return response(["errors" => ["trainer"=>"old courses cannot be modified"]], 400);

        if($schedule->user_id!=null)
        return response(["errors" => ["trainer"=>"the session cannot be modified, it has been reserved by name: ".$schedule->user->name.' | email: '.$schedule->user->email]], 400);

        $request->validate([
            'startDate' => ['required','date','after:today'],
            'endDate'=>['required','date','after:today'],
         ]);

        $rr= $request->all();
        $rr['startDate']=date('Y-m-d H:i:s', strtotime($request->post('startDate')));
        $rr['endDate']=date('Y-m-d H:i:s', strtotime($request->post('endDate')));
            $s=$schedule->forceFill($rr)->save();

        return $s;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Schedule $schedule)

    {


        if($request->user()->email!=$schedule->trainer->email)
        return response(["errors" => ["trainer"=>"that's another trainer course"]], 400);

        if($schedule->startDate<date('Y-m-d H:i:s'))
        return response(["errors" => ["trainer"=>"old courses cannot be removed"]], 400);


        if($schedule->user_id!=null)
        return response(["errors" => ["trainer"=>"the session cannot be removed, it already has been reserved by name: ".$schedule->user->name.' | email: '.$schedule->user->email]], 400);


       return $schedule->deleteOrFail();
    }
}
