<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notification as NotificationsNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->notifications;
    }
    public function show(Request $request,$id)
    {
       $notification= $request->user()->notifications()->where('id','=',$id)->first();
        $notification->markAsRead();
        return  $notification;
    }
}
