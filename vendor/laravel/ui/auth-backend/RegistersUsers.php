<?php

namespace Illuminate\Foundation\Auth;
use App\Models\RestaurantClient;
use App\Restorant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait RegistersUsers
{
    use RedirectsUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));
        if(!Auth::user()){
            $this->guard()->login($user);
        }else{
            if(Auth::user()->restaurant_id!=null){
                $flight = RestaurantClient::create([
                    'user_id' => $user->id,
                    'companie_id' => Auth::user()->restaurant_id,
                ]);
            }else{
               $restaurants=Restorant::where('user_id', Auth::user()->id)->get();
               if(count($restaurants)>0){
                    $flight = RestaurantClient::create([
                        'user_id' => $user->id,
                        'companie_id' => $restaurants[0]->id,
                    ]);
               }
            }
        }
        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }
}
