<?php

namespace App\Http\Controllers;

use App\Events\BroadCastNotifications;
use Illuminate\Http\Request;

class BroadCastNotificationsController extends Controller
{
    public function sendMessage(Request $request){
        $request->validate([
            "message"=>'string|required|max:30'
        ]);

        event(new BroadCastNotifications($request->message));
        return response()->json(['status' => 'Notification sent!']);
    }
}
