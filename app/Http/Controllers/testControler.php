<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class testControler extends Controller
{
    public function index(){
 return response()->json(User::all(),201);
    }
}
