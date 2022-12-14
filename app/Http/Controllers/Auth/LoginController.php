<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\User;
use App\Models\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    //protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectTo()
    {
        $function = $this->getIpLocation();
        $this->redirectTo = auth()->user()->hasRole('kitchen') ? route('live') : route('home');

        Log::create([
            'user_id' => auth()->user()->id,
            'ip' => $function->ip,
            'module' => 'ACCESO',
            'submodule' => 'LOGIN',
            'action' => 'Ingreso',
            'detail' => 'El usuario, ' .auth()->user()->name .' ha accedido a la plataforma',
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        //dd($this->redirectTo);
        return $this->redirectTo;
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function googleRedirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function googleHandleProviderCallback()
    {
        $user_google = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $user_google->email)->first();
        if (! $user) {
            $user = new User;
            $user->google_id = $user_google->id;
            $user->name = $user_google->name;
            $user->email = $user_google->email;
            $user->api_token = Str::random(80);
            $user->save();

            $user->assignRole('client');
        } else {
            if (empty($user->google_id)) {
                $user->google_id = $user_google->id;
            }

            $user->update();
        }

        // login
        Auth::loginUsingId($user->id);

        return redirect('/');
    }

    public function facebookRedirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function facebookHandleProviderCallback()
    {
        $user_facebook = Socialite::driver('facebook')->stateless()->user();
        $user = User::where('email', $user_facebook->email)->first();
        if (! $user) {
            $user = new User;
            $user->fb_id = $user_facebook->id;
            $user->name = $user_facebook->name;
            $user->email = $user_facebook->email;
            $user->api_token = Str::random(80);
            $user->save();

            $user->assignRole('client');
        } else {
            if (empty($user->fb_id)) {
                $user->fb_id = $user_facebook->id;
            }

            $user->update();
        }
        // login
        Auth::loginUsingId($user->id);

        return redirect('/');
    }
}
