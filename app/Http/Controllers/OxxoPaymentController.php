<?php

namespace App\Http\Controllers;

use App\Models\AesCrypt;
use App\Models\Authentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OxxoPaymentController extends Controller
{
    //
    public function oxxo_payment_charge(Request $request){
        $auth = new Authentication("mex@admin.com","secret123");
        $response = Http::withHeaders([
            'content-type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => $auth->getAuthorization(),
        ])->post($auth->getURL() . $auth->getOxxoPaymentEP() ,[
            'driver_token' => Auth::user()->sagmCredential->user_token,
            'driver_access_token' => AesCrypt::decrypt(Auth::user()->sagmCredential->access_token, "44745559505951506b633750"),
            'amount' => $request->input('amount')
        ]);
        return $response;
    }
}
