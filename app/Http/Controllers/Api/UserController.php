<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AesCrypt;
use App\Models\SagmCredential;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //
    private $private_key = "44745559505951506b633750";

    public function getUser(Request $request){
        $user_token = $request->user_token;
        $sagmCredentials = SagmCredential::all()->where('user_token', $user_token)->first();

        if($sagmCredentials != null){
            if(AesCrypt::decrypt($sagmCredentials->access_token, $this->private_key) == $request->access_token){
                $user = $sagmCredentials->user;
                return response()->json(["name" => $user->name, "email" => $user->email, "phone" => $user->phone], 200);
            }
        }

        return response()->json(["error" => "can´t resolve host request"], 400);
        
    }
}
