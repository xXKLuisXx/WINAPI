<?php

namespace App\Actions\Fortify;

use App\Models\AesCrypt;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Models\Role;
use App\Models\SagmCredential;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    private $privateKey = "Aes_256";
    private $url = "http://127.0.0.1:8001/api/";
    private $driverEP = "newDriver";
    private $clienteEP = "newClient";
    private $loginEP = "login";
    private $accessToken = "";
    private $tokenType = "";
    private $email = "mex@admin.com";
    private $password = "secret123";
    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
    */

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

    private function getAuthorization(){
        if($this->tokenType == null && $this->accessToken == null) {
            $this->getAuthToken();
        }
        return $this->tokenType." ".$this->accessToken;
    }
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->validate();

        $response = Http::withHeaders([
            'content-type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => $this->getAuthorization(),
        ])->get($this->url . $this->clienteEP);

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'password' => Hash::make($input['password']),
        ]);

        $client = $response->json();
        $sagmCredential = new SagmCredential();
        $sagmCredential->user_token = $client['client_token'];
        $sagmCredential->access_token = AesCrypt::encrypt($client['access_token'], bin2hex($this->privateKey));
        $sagmCredential->user_id = $user->id;
        $sagmCredential->save();

        return $user;
    }
}
