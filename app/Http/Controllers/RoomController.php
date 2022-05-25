<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\User;
use App\Models\Admin;
use App\Models\Trainer;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{

    }
    public function getUser($username)
    {
        $user = User::where('username', $username)->first();
        if (! $user)
        {
            $user = Trainer::where('username', $username)->first();
            if (! $user){
                $user = Admin::where('username', $username)->first();
            }

        }

    return $user;
    }
    public function getRooms(Request $request)
    {


        $array=[];
     $rooms= Room::where('FirstUserUsername','=',$request->user()->username)->orWhere('SecondUserUsername','=',$request->user()->username)->get();
        $i=0;
      foreach ($rooms as $room){
          if($room->FirstUserUsername==$request->user()->username){
           $user= $this->getUser($room->SecondUserUsername);
            $array[$i]=[
                'id'=>$room->id,
                'roomName'=>$room->SecondUserUsername,
               'username'=> $user->username,
               'profile_picture'=>$user->profile_picture
            ];
          }
          else if($room->SecondUserUsername==$request->user()->username){
            $user= $this->getUser($room->FirstUserUsername);
            $array[$i]=[
                'id'=>$room->id,
                'roomName'=>$user->name,
               'username'=> $user->username,
               'profile_picture'=>$user->profile_picture
            ];
          }
          $i++;
      }
      return $array;
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
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function show(Room $room)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Room $room)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Room  $room
     * @return \Illuminate\Http\Response
     */
    public function destroy(Room $room)
    {
        //
    }
}
