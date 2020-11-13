<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Cache\RateLimiter;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class SignInController extends Controller
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

    protected $maxAttempts = 5; 
    protected $decayMinutes = 5;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('revalidate');
        $this->middleware('guest');
    }

    public function form() {
        return view('auth.signin',['page_title' => '']);
    }

    public function signin(Request $request) {

        $rules = [
            'username'    => 'required',
            'password' => 'required'
        ];

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return redirect('/')->withErrors(['auth.throttle' => 'Too many login attempts, Please wait 5 minutes again']);
        }

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {

            $this->incrementLoginAttempts($request);
            return Redirect::to('/')->withErrors($validator);

        } else {

            $userdata = [
                'username'  => $request->get('username'),
                'password'  => $request->get('password')
            ];
            
            if (Auth::attempt($userdata)) {
                $this->clearLoginAttempts($request);
                return Redirect::to('/arena');
            }


            $this->incrementLoginAttempts($request);
            $validator->errors()->add('username', 'Username or password is incorrect');

            return Redirect::to('/')->withErrors($validator);
        }
        
    }

}
