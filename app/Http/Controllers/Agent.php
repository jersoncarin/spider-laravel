<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use App\Models\BetLogs;
use App\Models\User;
use App\Models\Referral;

class Agent extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function main_dashboard(Request $request)
    {

       
        if(Auth::user()->user_role == 1) {
            $code = Referral::where( 'master_agent_id' , Auth::user()->id )->where('agent_id',0)->first()->code;
        } else {
            $code = Referral::where( 'agent_id' , Auth::user()->id )->first()->code;
        }

        return view('agent.dashboard',[
            'page_title' => 'Dashboard',
            'total_commission' => bcdiv( BetLogs::where('user_id', Auth::user()->id )->where('action','Commission')->sum('amount') , 1 , 2),
            'total_active_users' => User::where('referral_code', $code )->where('activation',1)->count(),
            'total_pending_users' => User::where('referral_code', $code )->where('activation',0)->count(),
            'users' => User::where('referral_code', $code)
            ->where(function($query) use($request) {
                $query->where('username' , 'LIKE','%' . $request->q . '%')
                ->orWhere('credits' , 'LIKE','%' . $request->q . '%');
             })
            ->orderBy('registered_date','desc')->paginate(10)
        ]);
    }

    public function fetch_user_data(Request $request) {
        return response()->json(User::where('id',$request->id)->get(),200,[]);
    }

    public function transfer_credits(Request $request) {

        if($request->has('field_id') && $request->has('amount')) {

            $my_balance = Auth::user()->credits;
            $amount = $request->amount;

            if($amount > 0 && $my_balance >= $amount) {

                $auth = User::where('id',Auth::user()->id)->update([
                    'credits' => (Auth::user()->credits - $amount)
                ]);

                $user = DB::table('users')->where('id',$request->field_id)->update([
                    'credits' => DB::raw('credits + ' . $amount)
                ]);

                if($auth && $user) {
                    return redirect()->back()->withErrors(['alert_msg' => 'Successfully transfer your credits to this user, with amount of ' . $amount]);
                } else {
                    return redirect()->back()->withErrors(['alert_msg' => 'Failed to transfer credits!']);
                }

            } else {
                return redirect()->back()->withErrors(['alert_msg' => 'Insufficient credits to transfer!']);
            }

        } else {
            return redirect()->back()->withErrors(['alert_msg' => 'Unknown error occurred (?)']);
        }

    }

    public function approve_pending_users(Request $request) {

        if(Session::token() != $request->access_token) {
            return redirect()->back()->withErrors(['alert-msg' => 'Token Mismatch!']);
        }

        $user = User::where('id',$request->id)->update([
            'activation' => 1
        ]);

        if(!$user) {
            return redirect()->back()->withErrors(['alert-msg' => 'Failed to approve this user!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'User approved successfully!']);

    }

    public function transfer_credits_agent(Request $request) {

        if(Auth::user()->user_role != 1) {
            return redirect()->back()->withErrors(['alert-msg' => 'Error directives!']);
        }

        $user = User::where('username',$request->username)->first();
        $agents = Referral::where( 'master_agent_id' , Auth::user()->id )->where( 'master_agent_id' , '!=' , 0 )->get();

        if(!$user) {
            return redirect()->back()->withErrors(['alert-msg' => 'User not found!']);
        }

        $hasDone = false;

        foreach($agents as $agent) {

            if($agent->agent_id == $user->id) {

                DB::table('users')->where('id', $user->id)->update([
                    'credits' => DB::raw('credits + ' . $request->amount )
                ]);

                $hasDone = true;
                break;
            }
        }

        if(!$hasDone) {
            return redirect()->back()->withErrors(['alert-msg' => 'Failed to transfer points!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully transfer points!']);

    }
}
