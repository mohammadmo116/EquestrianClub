<?php

namespace App\Http\Controllers;

use Pusher\Pusher;
use Illuminate\Http\Request;
use App\Events\NewTournamentEvent;
use App\Notifications\NewTournamentNotification;
use Illuminate\Support\Facades\Log;

class pusherAuthController extends Controller
{
    /**
     *  /pusher/auth
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pusher = new Pusher(
            config("broadcasting.connections.pusher.key"),
            config("broadcasting.connections.pusher.secret"),
            config("broadcasting.connections.pusher.app_id"),
            config("broadcasting.connections.pusher.options"),
        );
        $socketId = $request->post("socket_id");
        $response = $pusher->socketAuth('private-my-channel'.$request->user->username,$socketId);
        Log::info($response);
        return $response;
    }
}
