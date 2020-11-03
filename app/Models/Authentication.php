<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Authentication extends Model
{
    use HasFactory;
    private $privateKey = "44745559505951506b633750";
    private $url = "https://winmexico.net/api/";
    private $driverEP = "newDriver";
    private $clienteEP = "newClient";
    private $oxxoPaymentEP = "oxxo_pay";
    private $loginEP = "login";
    private $accessToken = "";
    private $tokenType = "";
    private $email = "";
    private $password = "";

    public function __construct($email, $password){
        $this->email = $email;
        $this->password = $password;
    }

    private function getAuthToken(){
        $response = Http::withHeaders([
            'content-type' => 'application/json',
            'Accept' => 'application/json'
        ])->post($this->url.$this->loginEP, [
            'email' => $this->email,
            'password' => $this->password
        ]);

        $authTokens = $response->json();
        $this->accessToken = $authTokens['access_token'];
        $this->tokenType = $authTokens['token_type'];
    }

    public function getURL(){
        return $this->url;
    }

    public function getDriverEP(){
        return $this->driverEP;
    }

    public function getClientEP(){
        return $this->clienteEP;
    }

    public function getOxxoPaymentEP(){
        return $this->oxxoPaymentEP;
    }

    public function getAuthorization(){
        if($this->tokenType == null && $this->accessToken == null) {
            $this->getAuthToken();
        }
        return $this->tokenType." ".$this->accessToken;
    }
}
