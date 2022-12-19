<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Models\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ClientRegistered;
use Illuminate\Support\Facades\Validator;
use App\Notifications\WelcomeNotification;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        if(!isset($request->name) ){
           $this->middleware('guest');
         }
       
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $nuevafecha = strtotime ('-18 year' , strtotime(date('Y-m-d')));
        $nuevafecha = date ('d-m-Y',$nuevafecha);
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'number_identification' => ['required', 'string', 'min:7', 'max:10', 'unique:users'],
            'birth_date' => 'after:01/01/1920|before:'.$nuevafecha,
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
        if (strlen(config('settings.recaptcha_site_key')) > 2) {
            $rules['g-recaptcha-response'] = 'recaptcha';
        }
        if (config('settings.enable_birth_date_on_register') && config('settings.minimum_years_to_register')) {
            $rules['birth_date'] = 'required|date|date_format:Y-m-d|before:-'.config('settings.minimum_years_to_register').' years';
        }
        //dd($rules);

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $function = $this->getIpLocation();
        $user = User::create([
            'name' => $data['name'],
            'number_identification' => $data['number_identification'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
            'api_token' => Str::random(80),
            'birth_date' => isset($data['birth_date']) ? $data['birth_date'] : '',
        ]);
        Log::create([
            'user_id' => $user->id,
            'ip' => $function->ip,
            'module' => 'REGISTRO DE CLIENTE',
            'submodule' => '',
            'action' => 'Registro',
            'detail' => 'Se registró el cliente "' .$data['name'] .'", en la plataforma.',
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);

        $user->assignRole('client');
            
        //Send welcome email
        return $user;
    }

    protected function registered(Request $request, User $user)
    {   
        if(!isset($request->name) && !Auth::user()){
            return redirect($this->redirectPath());
          }
            
         
    }
}
