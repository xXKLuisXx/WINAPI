<?php

namespace App\Actions\Fortify;

use App\Models\AesCrypt;
use App\Models\Authentication;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use App\Models\SagmCredential;
use Illuminate\Support\Facades\Http;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
    */
    private $privateKey = "574b454c53326e7069634f4d";

    public function create(array $input)
    {
        $auth = new Authentication("mex@admin.com", "secret123");
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'role_id' => $input['role'],
            'password' => Hash::make($input['password']),
        ]);

        if($input['role'] == 1){
            $response = Http::withHeaders([
                'content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $auth->getAuthorization(),
            ])->get($auth->getURL() . $auth->getClientEP());

            $client = $response->json();
            $sagmCredential = new SagmCredential();
            $sagmCredential->user_token = $client['client_token'];
            $sagmCredential->access_token = AesCrypt::encrypt($client['access_token'], $this->privateKey);
            $sagmCredential->user_id = $user->id;
            $sagmCredential->save();

        }else if($input['role'] == 2){
            $response = Http::withHeaders([
                'content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $auth->getAuthorization(),
            ])->get($auth->getURL() . $auth->getDriverEP());

            $driver = $response->json();
            $sagmCredential = new SagmCredential();
            $sagmCredential->user_token = $driver['driver_token'];
            $sagmCredential->access_token = AesCrypt::encrypt($driver['access_token'], $this->privateKey);
            $sagmCredential->user_id = $user->id;
            $sagmCredential->save();

        }else{
            
        }

        return $user;
    }
}
