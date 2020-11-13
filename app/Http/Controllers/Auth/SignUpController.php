<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\UserInfo;


class SignUpController extends Controller
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

    use AuthenticatesUsers;

    protected $maxAttempts = 5; 
    protected $decayMinutes = 5;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {

        $this->middleware('revalidate');
        $this->middleware('guest');
    }

    public function form(Request $request) {

        $data = [
            'referral_code' => '5912',
            'page_title' => 'Sign Up'
        ];

        if($request->exists('r') && 
           !empty($request->r) &&
           is_numeric($request->r) &&
           strlen($request->r) == 4
        ) {

            $referral_count = DB::table('referral')->select('code')->where( ['code' => $request->r] )->count();

            if($referral_count > 0) {

                $data['referral_code'] = $request->r;

            } else {

                return redirect('/signup')->withErrors(['ref_code_not_exists' => 'Referral link does not exists']);
            }

        }

        return view('auth.signup',$data);
    }
    
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:8','unique:users'],
            'phone_number' => ['required', 'numeric', 'digits_between:10,13','unique:users'],
            'password' => ['required', 'string', 'min:4'],
            '_referral_token' => ['required', 'digits:4'],
        ], [ 
            'username.unique' => 'Username already exists',
            'username.max' => 'Username must be no longer 8 character length above',
            'phone_number.unique' => 'Phone number already exists',
            'phone_number.digits_between' => 'Phone number is not valid (11 or 12 digits is valid)',
            'password.min' => 'Password must be 4 char or longer'
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return redirect('/signup')->withErrors(['auth.throttle' => 'Too many login attempts, Please wait 5 minutes again']);
        }

        if($validator->fails()) {
            $this->incrementLoginAttempts($request);
            return redirect()->back()->withErrors($validator);
        }

        $referral_count = DB::table('referral')->select('code')->where( ['code' => $request->_referral_token] )->count();

        if($referral_count > 0) {

            $referreal_token = $request->_referral_token;

        } else {

            $referreal_token = 5912;

        }

        User::create([
            'username' => $request->username,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'referral_code' => $referreal_token,
        ]);

        $userdata = [
            'username'  => $request->get('username'),
            'password'  => $request->get('password')
        ];

        if(Auth::attempt($userdata)) {
            
            UserInfo::create([
                'user_id' => Auth::user()->id
            ]);

            $this->clearLoginAttempts($request);
            return redirect('/arena');
        }

        return redirect()->back();
    }

}
