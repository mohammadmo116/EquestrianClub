<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Trainer;
use App\Mail\RecoverCodes;
use App\Models\Price;
use App\Notifications\RecoverCodeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Notifications\Messages\MailMessage;

class ProfilePictureController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([

            'image' => 'required|file|max:2500'

         ]);

        if($request->hasFile('image')){

            $file=$request->file('image');

            if($file->isValid()){
               Storage::deleteDirectory('profilePics/'.$request->user()->username);
                $path=$file->storeAs('profilePics/'.$request->user()->username,$request->user()->username."_pf.".$file->getClientOriginalExtension(),['disk'=>'public']);

                $request->user()->forceFill([
                    'profile_picture'=> $path
                ])->save();

            }
            else{
                return Response::json([
                    'File'=>'file corrupted',
                ] );


            }

        }
    }
    public function getUser(User $user)
    {

        return $user;
    }
    public function getPoints()
    {

        return User::limit(3)->OrderBy('points','DESC')->get();
    }
    public function codeEmail(Request $request)
    {
        $request->validate([

            'email' => 'required'

         ]);

        $user = User::where('email', $request->email)->first();
        if (! $user)
        {
            $user = Trainer::where('email', $request->email)->first();
            if (! $user){
                $user = Admin::where('email', $request->email)->first();
            }

        }
        if (!$user){
          return response(["errors" => ["email"=>"user not found"]], 422);
        }
        if (!$user->two_factor_recovery_codes){
            return response(["errors" => ["email"=>"your 2FA is disabled"]], 400);
          }

          $user->notify(new RecoverCodeNotification());

    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return base64_encode(file_get_contents(storage_path('app').'/'.$request->user()->profile_picture)) ;
    }
    public function TUCount()
    {
        $uc=User::all()->count();
        $tc=Trainer::all()->count();
        $ppm=Price::first()->Price_Per_Min;
        return (response([
            'uc'=>$uc,
            'tc'=>$tc,
            'ppm'=>$ppm,
        ]));
    }
}
