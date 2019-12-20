<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
class MobileController extends Controller
{
    public function login(Request $request){
        $mail = $request->get('email');
        $password = $request->get('password');
        $users = User::where("email",$mail)->first();
        if (Hash::check($password, $users->password)) {
            return response()->json($users);
        }else{
            return response()->json([
                "logged" =>"no"
            ]);
        }
    }
}
