<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\HttpResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;

class AuthController extends Controller
{
    use HttpResponse;

    //***  Login Method  ***//
    public function login(LoginUserRequest $request){
        $request->validated($request->all());
        if(!Auth::attempt($request->only('email','password'))){
            return $this->error('','Credentials do not match', 401);
        }

        $user = User::where("email",$request->email)->first();

        return $this->success([
            "user"  =>  $user,
            "token" =>  $user->createToken('API Token of '. $user->name)->plainTextToken
        ]);
    }

    //***  Register Method  ***//
    public function register(StoreUserRequest $request){
        $request->validated($request->all());

        $user = User::create([
            "name"      =>  $request->name,
            "email"     =>  $request->email,
            "password"  =>  Hash::make($request->password)
        ]);

        
        return $this->success([
            "user"  =>  $user,
            "token" =>  $user->createToken('API Token of '.$user->name)->plainTextToken
        ]);
    }

    //***  Logout Method  ***//
    public function logout(){
        //return Auth::user();
        Auth::user()->currentAccessToken()->delete();
        return $this->success([
            "message"   =>  "You have successfully been logged out"
        ]);
    }
}
