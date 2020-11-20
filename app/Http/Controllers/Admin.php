<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\BetLogs;
use App\Models\User;
use App\Models\UserRequest;
use App\Models\ChatMessage;
use App\Models\UserInfo;
use App\Models\Referral;
use App\Models\RandNumbers;

class Admin extends Controller
{

      /**
     * Deduction for withdraw request
     *
     * @param $hasDeduction (boolean) toggle true/false
     * @return $deductionValue (float) toggle (percent - amount)
     */

    protected $hasDeduction = false;
    protected $deductionValue = 0.95;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        $dataSet = [
            'active_users' => DB::table('users')->where('user_role',0)->count(),
            'pending_users' => DB::table('users')->where('activation',0)->count(),
            'agent_users' => DB::table('users')->where(
                'user_role',1
            )->orWhere(
                'user_role',2
            )->count(),
            'visitors_count' => DB::table('visitors')->count(),
            'user_sources' => [
                'direct_sources' => DB::table('users')->where('referral_code','5912')->where('user_role',0)->count(),
                'referral_sources' => DB::table('users')->where('referral_code','!=','5912')->where('user_role',0)->count()
            ],
            'top_users' => DB::table('users')->join('users_info', 'users.id', '=', 'users_info.user_id')
            ->orderBy('users.credits','desc')->skip(0)->take(10)->get(),
            'earning_sources' => collect(DB::select('select extract(MONTH from created_at) as month , sum(amount) as total from site_bet_logs group by month,YEAR(created_at)')),
            'page_title' => 'Dashboard'
        ];

        return view('admin.dashboard',$dataSet);
    }

    public function visitors(Request $request) {

        $dataSet = [
            'visitor_count' => DB::table('visitors')->count(),
            'visitors_data' => DB::table('visitors')
            ->where('username','LIKE', '%' . $request->q . '%')
            ->orWhere('ip','LIKE', '%' . $request->q . '%')
            ->orderBy('lastseen','desc')->paginate(10),
            'page_title' => 'Activity Logs'
        ];

        return view('admin.visitors',$dataSet);
    }

    public function betlogs(Request $request) {
        
        return view('admin.betlogs',[
            'logs' => DB::table('bet_logs')->join('users','users.id','bet_logs.user_id')
             ->where('bet_logs.fight_no' , 'LIKE','%' . $request->q . '%')
             ->orWhere('bet_logs.fight_no' , 'LIKE','%' . $request->q . '%')
             ->orWhere('bet_logs.side' , 'LIKE','%' . $request->q . '%')
             ->orWhere('bet_logs.action' , 'LIKE','%' . $request->q . '%')
             ->orWhere('bet_logs.amount' , 'LIKE','%' . $request->q . '%')
             ->orWhere('bet_logs.bet' , 'LIKE','%' . $request->q . '%')
             ->orWhere('bet_logs.balance' , 'LIKE','%' . $request->q . '%')
             ->orWhere('bet_logs.fight_no' , 'LIKE','%' . $request->q . '%')
             ->orWhere('bet_logs.logs_date' , 'LIKE','%' . $request->q . '%')
             ->orWhere('users.username' , 'LIKE','%' . $request->q . '%')
             ->orderBy('bet_logs.logs_date','desc')->paginate(10),
            'total_logs' => BetLogs::count(),
            'page_title' => 'Bet Logs'
        ]);
    }

    public function active_users(Request $request) {
        return view('admin.users',[
            'page_title' => 'Active Users',
            'users_count' => DB::table('users')->where('user_role',0)->where('activation',1)->count(),
            'users' => DB::table('users')->leftJoin('users_info','users.id','users_info.user_id')
             ->select('users.id as parent_id_user','users.*','users_info.*')
             ->where('users.user_role',0)
             ->where('users.activation',1)
             ->where(function($query) use($request) {
                $query->where('users.username' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.phone_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.referral_code' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.credits' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.registered_date' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.first_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.last_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.address' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.city' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.country' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.zipcode' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.bio' , 'LIKE','%' . $request->q . '%');
             })
             ->orderBy('users.registered_date','desc')
             ->paginate(10)
        ]);
    }

    public function delete_active_user(Request $request) {

        if(Session::token() != $request->access_token) {
            return redirect()->back()->withErrors(['alert-msg' => 'Token Mismatch!']);
        }

        try {

            $user = User::where('id',$request->id)->first();

            DB::table('users')->where('id',$request->id)->delete();
            DB::table('users_info')->where('user_id',$request->id)->delete();
            DB::table('betting_logic')->where('user_id',$request->id)->delete();
            DB::table('bet_logs')->where('user_id',$request->id)->delete();
            DB::table('visitors')->where('username', $user->username )->delete();
            DB::table('requests')->where('user_id',$request->id)->delete();
            DB::table('customer_chat_message')->where('user_id',$request->id)->delete();
            DB::table('customer_chat_message')->where('user_id',$request->id)->delete();

        }  catch(\Illuminate\Database\QueryException $ex){ 

            return redirect()->back()->withErrors(['alert-msg' => 'Failed to delete this user!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully deleted this user!']);
    }

    public function edit_active_user(Request $request) {

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:8'],
            'phone_number' => ['required', 'numeric', 'digits_between:10,13'],
        ], [ 
            'username.unique' => 'Username already exists',
            'phone_number.unique' => 'Phone number already exists',
            'phone_number.digits_between' => 'Phone number is not valid (11 or 12 digits is valid)',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $field = User::where('id',$request->field_id)->first();
        $user = User::where('id',$request->field_id)->update([
            'username' => $request->username,
            'password' => $request->filled('password') ? Hash::make($request->password) : $field->password,
            'phone_number' => $request->phone_number
        ]);

        if(!$user) {
            return redirect()->back()->withErrors(['alert-msg' => 'No changes to this user!']);
        } 

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully edited this user!']);
    }

    public function fetch_users_json(Request $request) {
        return response()->json( User::where('id',$request->id)->get() , 200, []);
    }

    public function pending_users(Request $request) {
        return view('admin.pending_users',[
            'page_title' => 'Pending Users',
            'users_count' => DB::table('users')->where('user_role',0)->where('activation',0)->count(),
            'users' => DB::table('users')->leftJoin('users_info','users.id','users_info.user_id')
             ->select('users.id as parent_id_user','users.*','users_info.*')
             ->where('users.user_role',0)
             ->where('users.activation',0)
             ->where(function($query) use($request) {
                $query->where('users.username' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.phone_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.referral_code' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.credits' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.registered_date' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.first_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.last_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.address' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.city' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.country' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.zipcode' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.bio' , 'LIKE','%' . $request->q . '%');
             })
             ->orderBy('users.registered_date','desc')
             ->paginate(10)
        ]);
    }

    public function action_pending_users(Request $request) {

        if(Session::token() != $request->access_token) {
            return redirect('/admin/pending/users')->withErrors(['alert-msg' => 'Token Mismatch!']);
        }

        if($request->action == 'delete') {

            $this->delete_active_user($request);

        } else if($request->action == 'approve') {

            User::where("id",$request->id)->update([
                'activation' => 1
            ]);

        } else {
            
            return redirect('/admin/pending/users')->withErrors(['alert-msg' => 'Action is not defined!']);

        }

        return redirect('/admin/pending/users')->withErrors(['alert-msg' => ucfirst($request->action) . " execute successfully"]);
    }

    public function agent_users(Request $request) {
        return view('admin.agents',[
            'page_title' => 'Agents Users',
            'users_count' => DB::table('users')->where('activation',1)
            ->where(function($query) {
                $query->where('user_role',1)
                ->orWhere('user_role',2);
            })
            ->count(),
            'users' => DB::table('users')->join('users_info','users.id','users_info.user_id')
             ->leftJoin('referral','referral.agent_id', 'users.id')
             ->leftjoin('users as parent_agent','referral.master_agent_id','parent_agent.id')
             ->select('parent_agent.username as parent_username','users.*','users_info.*','users.id as parent_id_user')
             ->where('users.activation',1)
             ->where(function( $query ) {
                $query->where('users.user_role',1)
                ->orWhere('users.user_role',2);
             })
             ->where(function($query) use($request) {
                $query->where('users.username' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.phone_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.referral_code' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.credits' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.registered_date' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.first_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.last_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.address' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.city' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.country' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.zipcode' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.bio' , 'LIKE','%' . $request->q . '%');
             })
             ->orderBy('users.registered_date','desc')
             ->paginate(10)
        ]);
    }

    public function agent_users_add(Request $request) {

        $code = rand(1111,9999);
        $ref = Referral::pluck('code')->toArray();
        $hasMA = true;

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:8','unique:users'],
            'phone_number' => ['required', 'numeric', 'digits_between:10,13','unique:users'],
            'password' => ['required', 'string', 'min:4']
        ], [ 
            'username.unique' => 'Username already exists',
            'username.max' => 'Username must be no longer 8 character length above',
            'phone_number.unique' => 'Phone number already exists',
            'phone_number.digits_between' => 'Phone number is not valid (11 or 12 digits is valid)',
            'password.min' => 'Password must be 4 char or longer'
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        if($code == 5912 || in_array($code,$ref)) {
            $code = rand(1111,9999);
        }

        if(!$request->filled('master_agent_username')) {

            $hasMA = false;
            $system_id = 0;

        } elseif( $request->position_agent == 2 && ! $user = User::where('username',$request->master_agent_username)->where('user_role',1)->first() ) {
            return redirect()->back()->withErrors(['alert-msg' => 'Master agent not found!']);
        }

        $lastInsertedId = User::create([
            'username' => $request->username,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'referral_code' => 5912,
            'user_role' => $request->position_agent,
            'activation' => 1
        ]);

        UserInfo::create([
            'user_id' => $lastInsertedId->id
        ]);

        if($request->position_agent == 2) {

            Referral::create([
                'master_agent_id' => $hasMA ? $user->id : $system_id,
                'agent_id' => $lastInsertedId->id,
                'code' => $code
            ]);

        } else {

            Referral::create([
                'master_agent_id' => $lastInsertedId->id,
                'agent_id' => 0,
                'code' => $code
            ]);

        }

        return redirect()->back()->withErrors(['alert-msg' => 'Agent created successfully!']);

    }

    public function agent_users_delete(Request $request) {

        if(Session::token() != $request->access_token) {
            return redirect()->back()->withErrors(['alert-msg' => 'Token Mismatch!']);
        }

        $user = User::where('id',$request->id)->first();

        $rawStmt = "agent_id = ?";
        $rawArray = [
            $request->id
        ];

        if(isset($user) && $user->user_role == 1) {

           $rawStmt .= ' AND master_agent_id = ?';
           $rawArray = [
               0,
               $request->id
           ];

           Referral::where('master_agent_id',$request->id)->where('agent_id','!=',0)->update([
               'master_agent_id' => 0
           ]);

        }

        $referral = Referral::whereRaw($rawStmt,$rawArray)->pluck('code')->toArray();

        User::where('referral_code',array_shift($referral))->update([
            'referral_code' => 5912
        ]);

        Referral::whereRaw($rawStmt,$rawArray)->delete();

        try {

            $user = User::where('id',$request->id)->first();

            DB::table('users')->where('id',$request->id)->delete();
            DB::table('users_info')->where('user_id',$request->id)->delete();
            DB::table('betting_logic')->where('user_id',$request->id)->delete();
            DB::table('bet_logs')->where('user_id',$request->id)->delete();
            DB::table('visitors')->where('username', $user->username )->delete();
            DB::table('requests')->where('user_id',$request->id)->delete();
            DB::table('customer_chat_message')->where('user_id',$request->id)->delete();
            DB::table('customer_chat_message')->where('user_id',$request->id)->delete();

        }  catch(\Illuminate\Database\QueryException $ex){ 

            return redirect()->back()->withErrors(['alert-msg' => 'Failed to delete this agent!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully deleted this agent!']);

    }

    public function agents_users_edit(Request $request) {

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:8'],
            'phone_number' => ['required', 'numeric', 'digits_between:10,13'],
        ], [ 
            'username.unique' => 'Username already exists',
            'username.max' => 'Username must be no longer 8 character length above',
            'phone_number.unique' => 'Phone number already exists',
            'phone_number.digits_between' => 'Phone number is not valid (11 or 12 digits is valid)',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = User::where('id',$request->field_id)->first();

        $role = $user->user_role;

        if( ($request->user_role == 2 && $user->user_role == 1) || ($request->filled('master_agent_username') && $request->user_role == 2)) {

            if ( $request->filled('master_agent_username') && $request->master_agent_username == strtolower('sys_config')) {
                $ma_user = 0;
            } elseif( $request->filled('master_agent_username') ) {
                $ma_user = User::where('username',$request->master_agent_username)->where('user_role',1)->first();
                if(!$ma_user) return redirect()->back()->withErrors(['alert-msg' => 'Master agent not found!']);
            } else {
                $nothing_to_update = true;
            }

            if(!isset($nothing_to_update)) {

                if($request->user_role == 2 && $user->user_role == 2) {

                    Referral::where('agent_id',$request->field_id)->update([
                        'master_agent_id' => !is_scalar($ma_user) ? $ma_user->id : $ma_user
                    ]);

                } else {

                    Referral::where('master_agent_id',$request->field_id)->where('agent_id',0)->update([
                        'master_agent_id' => !is_scalar($ma_user) ? $ma_user->id : $ma_user,
                        'agent_id' => $request->field_id
                    ]);        

                }

            } else {

                return redirect()->back()->withErrors(['alert-msg' => 'Agent not set! if you wish to use system master agent (type sys_config) to assign it']);

            }
            
            Referral::where('master_agent_id',$request->field_id)->where('agent_id','!=',0)->update([
                'master_agent_id' => 0
            ]);

            $role = 2;

        } elseif ($request->user_role == 1 && $user->user_role == 2) {

            Referral::where('agent_id',$request->field_id)->update([
                'master_agent_id' => $request->field_id,
                'agent_id' => 0
            ]);  

            $role = 1;

        }

        User::where('id',$request->field_id)->update([
            'user_role' => $role,
            'username' => $request->username,
            'phone_number' => $request->phone_number,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);


        return redirect()->back()->withErrors(['alert-msg' => 'Successfully edited this agent!']);

    }

    public function requests(Request $request) {

        return view('admin.requests',[
            'request_count_deposit' => UserRequest::where('type','deposit')->count(),
            'request_count_withdraw' => UserRequest::where('type','withdraw')->count(),
            'requests_deposit' => DB::table('requests')->join('users','users.id','requests.user_id')
                ->select('users.username' , 'requests.*')
                ->where('type','deposit')
                ->where(function($query) use($request) {
                $query->where('type' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.username' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.type' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.amount' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.sender_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.reciever_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.reference_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.account_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.account_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.contact_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.status' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.request_date' , 'LIKE','%' . $request->q . '%');
             })->orderBy('requests.request_date', 'desc')
             ->paginate(10),

             'requests_withdraw' => DB::table('requests')->join('users','users.id','requests.user_id')
             ->select('users.username' , 'requests.*')
             ->where('type','withdraw')
             ->where(function($query) use($request) {
                $query->where('type' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.username' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.type' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.amount' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.sender_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.reciever_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.reference_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.account_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.account_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.contact_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.status' , 'LIKE','%' . $request->q . '%')
                ->orWhere('requests.request_date' , 'LIKE','%' . $request->q . '%');
          })->orderBy('requests.request_date', 'desc')
          ->paginate(10),
          'page_title' => 'Requests',
          'deduction_per' => $this->deductionValue,
          'hasDeduction' => $this->hasDeduction
        ]);
    }

    public function requests_approve(Request $request) {

        if(Session::token() != $request->access_token) {
            return redirect()->back()->withErrors(['alert-msg' => 'Token Mismatch!']);
        }

        $userRequest = UserRequest::where('id',$request->id)->first();
        $userBalance = User::where('id',$userRequest->user_id)->first()->credits;

        if($userRequest && ($userRequest->type == 'deposit') ) {

            DB::table('users')->where('id', $userRequest->user_id)->update([
                'credits' => DB::raw('credits + ' . $userRequest->amount)
            ]);

        } else if($userRequest && ($userRequest->type == 'withdraw') ) {

            if($userBalance && ($userBalance >= $userRequest->amount )) {

                DB::table('users')->where('id',$userRequest->user_id)->update([
                    'credits' => DB::raw('credits - ' . $userRequest->amount)
                ]);

                if( $this->hasDeduction ) {

                    DB::table('requests')->where('id',$userRequest->id)->update([
                        'withdraw_msg' => 'A deduction of 5% as commission totalling to ' . floatval($userRequest->amount * $this->deductionValue) . ' pesos'
                    ]);

                }

            } else {
                return redirect()->back()->withErrors(['alert-msg' => 'User has insufficient balance to withdraw this amount!']);
            }

        } else {
            return redirect()->back()->withErrors(['alert-msg' => 'Unknown error (?)']);
        }


        $request = DB::table('requests')->where('id',$request->id)->update([
            'status' => 'Approved'
        ]);

        if(!$request) {
            return redirect()->back()->withErrors(['alert-msg' => 'Unable to approve this request!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully approved this request!']);

    }

    public function requests_delete_or_reject(Request $request) {

        if(Session::token() != $request->access_token) {
            return redirect()->back()->withErrors(['alert-msg' => 'Token Mismatch!']);
        }

        $delete = filter_var($request->delete, FILTER_VALIDATE_BOOLEAN);

        if( $delete ) {

            $msg_ = "delete";

            $request = UserRequest::where('id',$request->id)->delete();

        } else {

            $msg_ = "rejected";

            $request = DB::table('requests')->where('id',$request->id)->update([
                'status' => 'Rejected'
            ]);

        }

        if(!$request) {
            return redirect()->back()->withErrors(['alert-msg' => 'Unable to ' . $msg_ . ' this request!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully ' . $msg_ . ' this request!']);
        
    }

    public function csc_requests(Request $request) {

        return view('admin.csc_req',[
            'csc_request_count' => DB::table('customer_chat_subject')->count(),
            'csc_requests' => DB::table('customer_chat_subject')
            ->join('users','users.id','customer_chat_subject.user_id')
            ->select('users.username as username', 'customer_chat_subject.*')
            ->where(function($query) use($request) {
                $query->where('users.username' , 'LIKE','%' . $request->q . '%')
                ->orWhere('customer_chat_subject.subject_name' , 'LIKE','%' . $request->q . '%');
            })->orderBy('customer_chat_subject.created_at','desc')
            ->paginate(10),
            'page_title' => 'Customer Service Chat'
        ]);
    }

    public function csc_load_requests(Request $request) {

        $load_sub = DB::table('customer_chat_subject')->where('id',$request->id)->first();

        if(Session::token() != $request->access_token) {
            return redirect()->back()->withErrors(['alert-msg' => 'Token Mismatch!']);
        } else if(!$load_sub) {
            return redirect()->back()->withErrors(['alert-msg' => 'Can\'t find this csc request!']);
        }

        return view('admin.csc_req_load',[
            'subjects' => $load_sub
        ]);

    }

    public function csc_load_chat(Request $request) {

        $chats = ChatMessage::where('subject_id',$request->s_id)->orderBy('id','desc')->paginate(20)->toArray();

        $reversed = array_reverse($chats['data']);
        $chats['data'] = $reversed;

        if(!$chats || session()->token() != $request->token) return response()->json(['status' => false],200,[],JSON_UNESCAPED_UNICODE);

        return response()->json(['status' => true,'chats' => $chats],200,[],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }

    public function csc_send_chat(Request $request) {

        if($request->has('message') && $request->has('subject_id')) {

            ChatMessage::create([
                'user_id' => Auth::user()->id,
                'message' => strip_tags($request->message),
                'sender' => 'CSR',
                'subject_id' => $request->subject_id

            ]);

            DB::table('customer_chat_subject')->where('id',$request->subject_id)->update([
                'hasReply' => 1
            ]);

            return response()->json(['status' => true],200,[],JSON_UNESCAPED_UNICODE);

        } else {
            return response()->json(['status' => false],200,[],JSON_UNESCAPED_UNICODE);
        }
    }

    public function csc_close_requests(Request $request) {

        if(Session::token() != $request->access_token) {
            return redirect()->back()->withErrors(['alert-msg' => 'Token Mismatch!']);
        }

        $reopen = filter_var($request->reopen, FILTER_VALIDATE_BOOLEAN);

        $status = 1;

        if($reopen) {
            $status = 0;
        }

        $ccs = DB::table('customer_chat_subject')->where('id',$request->id)->update([
            'status' => $status
        ]);

        if(!$ccs) {
            return redirect()->back()->withErrors(['alert-msg' => 'Already closed!']);
        }

        if($reopen) {
            return redirect()->back()->withErrors(['alert-msg' => 'This Chat Request has been reopen!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'This Chat Request has been closed!']);

    }

    public function official_users(Request $request) {
        return view('admin.admin',[
            'page_title' => 'Active Users',
            'users_count' => DB::table('users')->whereIn('user_role',[3,4,5])->count(),
            'users' => DB::table('users')->leftJoin('users_info','users.id','users_info.user_id')
             ->select('users.id as parent_id_user','users.*','users_info.*')
             ->whereIn('user_role',[3,4,5])
             ->where(function($query) use($request) {
                $query->where('users.username' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.phone_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.referral_code' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.credits' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.registered_date' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.first_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.last_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.address' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.city' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.country' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.zipcode' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.bio' , 'LIKE','%' . $request->q . '%');
             })
             ->orderBy('users.registered_date','desc')
             ->paginate(10)
        ]);
    }

    public function edit_official_user(Request $request) {

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:8'],
            'phone_number' => ['required', 'numeric', 'digits_between:10,13'],
        ], [ 
            'username.unique' => 'Username already exists',
            'phone_number.unique' => 'Phone number already exists',
            'phone_number.digits_between' => 'Phone number is not valid (11 or 12 digits is valid)',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $field = User::where('id',$request->field_id)->first();
        $user = User::where('id',$request->field_id)->update([
            'username' => $request->username,
            'password' => $request->filled('password') ? Hash::make($request->password) : $field->password,
            'phone_number' => $request->phone_number,
            'user_role' => $request->user_role
        ]);

        if(!$user) {
            return redirect()->back()->withErrors(['alert-msg' => 'No changes to this official user!']);
        } 

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully edited this official user!']);
    }

    public function add_official_user(Request $request) {

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:8'],
            'phone_number' => ['required', 'numeric', 'digits_between:10,13'],
        ], [ 
            'username.unique' => 'Username already exists',
            'phone_number.unique' => 'Phone number already exists',
            'phone_number.digits_between' => 'Phone number is not valid (11 or 12 digits is valid)',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'user_role' => $request->user_role,
            'referral_code' => 5912,
            'credits' => 105,
            'activation' => 1
        ]);

        if(!$user) {
            return redirect()->back()->withErrors(['alert-msg' => 'Failed adding official user!']);
        } 

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully added this official user!']);
    }

    public function verifier_pending_users(Request $request) {
        return view('verifier.pending_users',[
            'page_title' => 'Verifier Dashboard',
            'users_count' => DB::table('users')->where('user_role',0)->where('activation',0)->count(),
            'users' => DB::table('users')->leftJoin('users_info','users.id','users_info.user_id')
             ->select('users.id as parent_id_user','users.*','users_info.*')
             ->where('users.user_role',0)
             ->where('users.activation',0)
             ->where(function($query) use($request) {
                $query->where('users.username' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.phone_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.referral_code' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.credits' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users.registered_date' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.first_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.last_name' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.address' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.city' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.country' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.zipcode' , 'LIKE','%' . $request->q . '%')
                ->orWhere('users_info.bio' , 'LIKE','%' . $request->q . '%');
             })
             ->orderBy('users.registered_date','desc')
             ->paginate(10)
        ]);
    }

    public function verifier_action_pending_users(Request $request) {

        if(Session::token() != $request->access_token) {
            return redirect()->back()->withErrors(['alert-msg' => 'Token Mismatch!']);
        }

        if($request->action == 'delete') {

            $this->delete_active_user($request);

        } else if($request->action == 'approve') {

            User::where("id",$request->id)->update([
                'activation' => 1
            ]);

        } else {
            
            return redirect()->back()->withErrors(['alert-msg' => 'Action is not defined!']);

        }

        return redirect()->back()->withErrors(['alert-msg' => ucfirst($request->action) . " execute successfully"]);
    }

    public function gcash_account(Request $request) {
        return view('admin.gcash',[
            'gcash_count' => RandNumbers::count(),
            'numbers' => RandNumbers::where(function($query) use($request) {
                $query->where('account_number' , 'LIKE','%' . $request->q . '%')
                ->orWhere('account_name' , 'LIKE','%' . $request->q . '%');
             })
             ->orderBy('id','desc')
             ->paginate(10),
             'page_title' => 'GCASH Numbers'
        ]);
    }


    public function gcash_account_delete(Request $request) {

        if(Session::token() != $request->access_token) {
            return redirect()->back()->withErrors(['alert-msg' => 'Token Mismatch!']);
        }

        $delete = RandNumbers::where('id',$request->id)->delete();

        if(!$delete) {
            return redirect()->back()->withErrors(['alert-msg' => 'Failed to delete this gcash account!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'Gcash account deleted successfully!']);

    }

    public function fetch_gcash_account(Request $request) {
        return response()->json( RandNumbers::where('id',$request->id)->get() , 200, []);
    }


    public function gcash_account_add(Request $request) {

        $validator = Validator::make($request->all(), [
            'account_number' => ['required', 'numeric','unique:rand_numbers'],
            'account_name' => ['required', 'string','unique:rand_numbers']
        ], [ 
            'account_number.unique' => 'Account Number already exists',
            'account_name.unique' => 'Account Name number already exists',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $create = RandNumbers::create([
            'account_number' => $request->account_number,
            'account_name' => $request->account_name
        ]);

        if(!$create) {
            return redirect()->back()->withErrors(['alert-msg' => 'Failed to create this gcash account!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully created this gcash account successfully!']);

    }

    public function gcash_account_edit(Request $request) {

        $validator = Validator::make($request->all(), [
            'account_number' => ['required', 'numeric'],
            'account_name' => ['required', 'string']
        ], [ 
            'account_number.unique' => 'Account Number already exists',
            'account_name.unique' => 'Account Name number already exists',
        ]);

        if($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $update = RandNumbers::where('id',$request->id)->update([
            'account_number' => $request->account_number,
            'account_name' => $request->account_name
        ]);

        if(!$update) {
            return redirect()->back()->withErrors(['alert-msg' => 'Nothing to update this gcash account!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully updated this gcash account successfully!']);

    }

    public function transfer_request_points(Request $request) {

        $exists = User::where('username',$request->username)->first();

        if(!$exists) {
            return redirect()->back()->withErrors(['alert-msg' => 'Username not found!']);
        } elseif(!is_numeric($request->amount)) {
            return redirect()->back()->withErrors(['alert-msg' => 'Amount is not valid!']);
        }

        if($request->operand == 1) {
            $operand = '+ ';
        } else {
            $operand = '- ';
        }

        $user = DB::table('users')->where('username',$request->username)->update([
            'credits' => DB::raw('credits ' . $operand . $request->amount)
        ]);

        if(!$user) {
            return redirect()->back()->withErrors(['alert-msg' => 'Can\'t update the credits!']);
        }

        return redirect()->back()->withErrors(['alert-msg' => 'Successfully transfer to ' . $request->username . ' with amount of ' . $request->amount ]);

    }

}
