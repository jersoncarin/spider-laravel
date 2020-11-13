<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserInfo;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user(Request $request)
    {

        $user_info = UserInfo::where('user_id',Auth::user()->id)->first();

        $referral = [];
        
        if(Auth::user()->user_role == 1) {
            $referral = Referral::where('master_agent_id',Auth::user()->id)->first();
        } elseif (Auth::user()->user_role == 2) {
            $referral = Referral::where('agent_id',Auth::user()->id)->first();
        }

        return view('user.user',['page_title' => 'My Profile', 'user' => $user_info,'referral' => $referral]);
    }

    public function update(Request $request) {
      
        if($request->has('password') && !empty($request->password)) {
            $user = User::find(Auth::user()->id);
            $user->password = Hash::make($request->password);
            $user->save();
        }

        foreach($request->post() as $post => $value) {
            if( $post != '_token' && $post != 'password' )
                $data[$post] = $value;
        }
        
        $updated = UserInfo::where('user_id',Auth::user()->id)->update($data);

        $message = 'Failed to update your user info';

        if($updated) {
            $message = 'Successfully updated your user info';
        }

        return redirect()->back()->withErrors(['message' => $message]);
    }
}
