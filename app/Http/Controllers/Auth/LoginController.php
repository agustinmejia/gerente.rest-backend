<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;

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
        dd($auth_user);
        // if($auth_user){
        //     if(!empty($auth_user->email)){
        //         $user = User::where('email', $auth_user->email)->first();

        //         if($user){
        //             Auth::login($user, true);
        //         }else{
        //             $cliente = Cliente::create([
        //                 'razon_social' => $auth_user->name
        //             ]);

        //             $user = User::create([
        //                         'name' => $auth_user->name,
        //                         'email' => $auth_user->email,
        //                         'password' => Hash::make(str_random(10)),
        //                         'avatar' => $auth_user->avatar,
        //                         'tipo_login' => 'facebook',
        //                         'cliente_id' => $cliente->id
        //                     ]);

        //             Auth::login($user, true);
        //         }
        //         return redirect()->route('ecommerce_home');
        //     }
        // }
    }
}
