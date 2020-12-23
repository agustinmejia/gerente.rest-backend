<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Medels
use App\Models\User;
use App\Models\Person;
use App\Models\Owner;
use App\Models\Company;

class APIController extends Controller
{
    public function login(Request $request){
        $user = null;
        $token = null;

        if($request->social_login){
            $user = User::where('email', $request->email)->first() ?? $this->newDriver($request);
            $token = $user->createToken('appxiapi')->accessToken;

            // Actualizar token de firebase
            if($request->firebase_token){
                User::where('id', $user->id)->update([
                    'firebase_token' => $request->firebase_token
                ]);
            }
        }else{
            $credentials = ['email' => $request->email, 'password' => $request->password];
            if (Auth::attempt($credentials)) {
                $auth = Auth::user();
                $token = $auth->createToken('gerente.rest')->accessToken;
                $user = User::where('id', $auth->id)->first();
                // Actualizar token de firebase
                if($request->firebase_token){
                    $user_update = User::find($user->id);
                    $user_update->firebase_token = $request->firebase_token;
                    $user_update->save();
                }
            }
        }

        if($user && $token){
            return response()->json(['user' => $user, 'token' => $token]);
        }else{
            return response()->json(['error' => "credentials don't exist"]);
        }
    }

    public function register(Request $request){
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->firstName,
                'email' => $request->email,
                'password' => bcrypt($request->password)
            ]);

            // create person
            $person = Person::create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone' => $request->phone
            ]);

            // Create owner
            $owner = Owner::create([
                'person_id' => $person->id,
                'user_id' => $user->id
            ]);

            $company = Company::create([
                'owner_id' => $owner->id,
                'name' => $request->companyName,
                'city_id' => $request->city_id,
            ]);

            DB::commit();
            return response()->json(['user' => $user]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['error' => $th]);
        }
    }
}
