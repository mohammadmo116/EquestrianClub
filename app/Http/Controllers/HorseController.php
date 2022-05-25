<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Horse;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class HorseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return Horse::with('owner')->get();

    }
    public function getUserHorses(Request $request)
    {
        return  $request->user()->horses()->get();

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


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
       $request->validate([
        'roomId' => ['required',Rule::unique(Horse::class),'numeric'],
        'birthday'=>['required','date','before_or_equal:today'],
        'name'=>['required'],
        'gender'=>['required'],
     ]);
$user->horses()->create([
    'roomId'=>$request->post('roomId'),
    'name'=>$request->post('name'),
    'birthday'=>$request->post('birthday'),
    'gender'=>$request->post('gender'),

]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Horse  $horse
     * @return \Illuminate\Http\Response
     */
    public function show(Horse $horse)
    {
        return $horse->with('owner')->get()->where('id','=',$horse->id)->first();

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Horse  $horse
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Horse $horse)
    {
        $user = User::where('email', $request->email)->first();
        if (! $user)
        {
            $user = Trainer::where('email', $request->email)->first();
            if (! $user){
                $user = Admin::where('email', $request->email)->first();
            }

        }
        if (!$user){
          return response(["errors" => ["email"=>"user not found"]], 400);
        }
        $request->validate([
         'roomId' => ['required',Rule::unique(Horse::class)->ignore($horse->id),'numeric'],
         'birthday'=>['required','date','before_or_equal:today'],
         'name'=>['required'],
         'gender'=>['required'],
      ]);

    $horse->forceFill([
          'owner_id'=> $user->id,
          'owner_type'=> $user::class,
        'roomId'=>$request->post('roomId'),
        'name'=>$request->post('name'),
        'birthday'=>$request->post('birthday'),
        'gender'=>$request->post('gender'),

      ])->save();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Horse  $horse
     * @return \Illuminate\Http\Response
     */
    public function destroy(Horse $horse)
    {
        return $horse->deleteOrFail();
    }
}
