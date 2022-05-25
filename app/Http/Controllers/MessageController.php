<?php

namespace App\Http\Controllers;

use App\Events\chatEvent;
use App\Models\Room;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }
    public function getMessages(Request $request,Room $room)
    {
        return $room->messages()->orderBy('created_at')->with('room')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,Room $room)
    {

        $message=$room->messages()->create([
            'Username'=>$request->user()->username,
            'MessageText'=>$request->post('MessageText'),
        ]);
        $to='';
        if($room->FirstUserUsername==$request->user()->username)
        $to=$room->SecondUserUsername;
        else
        $to=$room->FirstUserUsername;

          event(new chatEvent($request->post('MessageText'),$to,$request->user()->username,$message->id));

          return $message;


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
