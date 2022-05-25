<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TokenAuthController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'email' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
         if (! $user)
        $user = User::where('username', $request->email)->first();
        if($user)
        $type="";

        if (! $user)
        {

            $user = Trainer::where('email', $request->email)->first();
            if (! $user)
            $user = Trainer::where('username', $request->email)->first() ;
            if($user)
            $type="trainer/";

            if (! $user){
                $user = Admin::where('email', $request->email)->first();
                if (! $user)
                $user = Admin::where('username', $request->email)->first() ;
                if($user)
                $type="admin/";
            }

        }

            if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

    $request->session()->put([
        'login.id' => $user->getKey(),
        'login.remember' => $request->filled('remember'),
    ]);
        if($user->two_factor_recovery_codes){
        return response()->json(["two_factor"=>true,"type"=>$type],200);}
        else{
            return response()->json(['token'=>$user->createToken($request->device_name)->plainTextToken,
                                "two_factor"=>false,
                                "type"=>$type],200);
        }


        // return $user->createToken($request->device_name)->plainTextToken;
      }

      public function destroy(Request $request){

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $request->user()->tokens()->delete();

      }
}

