<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Socialite;

// Controllers
use App\Http\Controllers\APIController;

// Models
use App\Models\User;
use App\Models\City;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToSocialProvider($social)
    {
        return Socialite::driver($social)->redirect();
    }

    public function handleProviderSocialCallback($social)
    {
        $auth_user = Socialite::driver($social)->user(); // Fetch authenticated user
        if($auth_user){
            $email = $auth_user->email ?? 'example@gerente.rest';
            $user = User::where('email', $email)->with(['roles', 'suscription'])->where('status', 1)->where('deleted_at', NULL)->first();

            if($user){
                $token = $user->createToken('gerente.rest')->accessToken;
                $user_company_info = (new APIController)->user_company_info($user);
                $company = $user_company_info['company'];
                $branch = $user_company_info['branch'];
                $auth_login = json_encode(['user' => $user, 'company' => $company, 'branch' => $branch,'token' => $token]);
                return view('security.users.create-socialite', compact('auth_login'));
            }else{
                $city = City::where('deleted_at', NULL)->first();
                $request = new Request();
                $request->replace([
                    'firstName' => $auth_user->name,
                    'email' => $email,
                    'avatar' => $auth_user->avatar,
                    'password' => Str::random(10),
                    'phone' => '',
                    'city' => $city ? $city->id : NULL,
                    'companyName' => "Restaurante de ".$auth_user->name,
                ]);

                $auth_login = (new APIController)->register($request);
                return view('security.users.create-socialite', compact('auth_login'));
            }
        }
    }
}
