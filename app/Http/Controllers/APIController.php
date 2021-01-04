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
use App\Models\Branch;

class APIController extends Controller
{
    // Auth
    public function login(Request $request){
        $user = null;
        $token = null;

        if($request->social_login){
            $user = User::where('email', $request->email)->with(['owner.person'])->first() ?? $this->new_owner($request);
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
                $user = User::where('id', $auth->id)->with(['owner.person'])->first();
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
                'city_id' => $request->city,
            ]);

            $user = User::where('id', $user->id)->with(['owner.person'])->first();
            $token = $user->createToken('gerente.rest')->accessToken;

            DB::commit();
            return response()->json(['user' => $user, 'token' => $token]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['error' => $th]);
        }
    }

    // Company
    public function my_company($id){
        $company = Company::find($id);
        return response()->json(['company' => $company]);
    }

    public function my_company_update_images(Request $request){
        $logo = $this->save_image($request->file('logo'), 'companies');
        $banner = $this->save_image($request->file('banner'), 'companies');

        try {
            $company = Company::find($request->id);
            if($logo){
                $company->logos = $logo;
            }
            if($banner){
                $company->banners = $banner;
            }
            $company->save();
            return response()->json(['logo' => $logo, 'banner' => $banner]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th]);
        }
    }

    public function my_company_update(Request $request){
        try {
            $company = Company::find($request->id);
            $company->name = $request->name;
            $company->slogan = $request->slogan;
            $company->city_id = $request->city_id;
            $company->phones = $request->phones;
            $company->address = $request->address;
            $company->small_description = $request->small_description;
            $company->save();
            return response()->json(['company' => $company]);
        } catch (\Throwable $th) {
            return response()->json(['error' => $th]);
        }
    }

    public function my_company_branch_list($id){
        try{
            $company = Company::where('owner_id', $id)->first();
            $branches = Branch::where('company_id', $company->id)->get();
            return response()->json(['branches' => $branches]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['error' => $th]);
        }
    }

    // Branches
    public function my_company_branch_create(Request $request){
        DB::beginTransaction();
        try {
            $company = Company::where('owner_id', $request->ownerId)->first();
            $branch = Branch::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'city' => $request->city,
                'location' => $request->location,
                'phones' => $request->phones,
                'address' => $request->address
            ]);

            DB::commit();
            return response()->json(['branch' => $branch]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['error' => $th]);
        }
    }

}
