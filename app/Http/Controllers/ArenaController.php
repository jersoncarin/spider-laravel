<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fights;
use Illuminate\Support\Facades\Auth;
use App\Models\BettingLogic;
use App\Models\UserRequest;
use App\Models\User;
use App\Models\BetLogs;
use App\Models\MsgLogs;
use App\Models\Referral;
use App\Models\SiteLogs;
use App\Models\ChatSubject;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\DB;

class ArenaController extends Controller
{
    protected $percentage = 0.85;
    protected $payout = 0.95;
    protected $agent_commission = 0.01;
    protected $master_agent_commission = 0.04;
    protected $site_commission = 0.05;
    protected $min_bet = 100;
    protected $hasDeduction = false;
    protected $hasReversedAgent = true;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('revalidate');
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $fight = Fights::orderBy('id','desc')->skip(0)->take(1)->first();

        $reg_stamp = strtotime(Auth::user()->registered_date);
        $disabled_viewing = false;
        $decayMinutes = 60 * 15;

        if(time() > ($reg_stamp + $decayMinutes))
          $disabled_viewing = true;

        $user = (object) ['disabled_viewing' => $disabled_viewing];

        $data['left_side_total_bet'] = BettingLogic::where('fight_id',$fight->id)->where('side',0)->sum('amount');
        $data['right_side_total_bet'] = BettingLogic::where('fight_id',$fight->id)->where('side',1)->sum('amount');

        $left_side_user_bet = BettingLogic::where('fight_id',$fight->id)->where('user_id',Auth::user()->id)->where('side',0)->sum('amount');
        $right_side_user_bet = BettingLogic::where('fight_id',$fight->id)->where('user_id',Auth::user()->id)->where('side',1)->sum('amount');

      
        if($data['left_side_total_bet'] && $data['right_side_total_bet']) {
            $left_multiplier = (($data['right_side_total_bet'] * $this->percentage) / $data['left_side_total_bet']) ?? 0;
            $left_multiplier = bcdiv($left_multiplier + 1, 1, 2);
                
            $right_multiplier = (($data['left_side_total_bet'] * $this->percentage) / $data['right_side_total_bet']) ?? 0;
            $right_multiplier = bcdiv($right_multiplier + 1 , 1, 2);
        } else {
            $left_multiplier = 0;
            $right_multiplier = 0;
        }

        $request->session()->put('left_multiplier',$left_multiplier);
        $request->session()->put('right_multiplier',$right_multiplier);

        $data['left_side_total_percentage'] = ( $left_multiplier * 100 ) . '%' ?? '0%';
        $data['right_side_total_percentage'] = ( $right_multiplier * 100 ) . '%' ?? '0%';
        $data['current_fight_no'] = $fight->fight_no;
        $data['left_user_payout'] = bcdiv( ($left_side_user_bet * $left_multiplier),1,2);
        $data['right_user_payout'] = bcdiv( ($right_side_user_bet * $right_multiplier),1,2);
        $data['cant_bet'] = (bool) $fight->betting_status;
        $data['betting_logs'] = BetLogs::where('user_id',Auth::user()->id)->orderBy('logs_date','desc')->take(5)->get();
        $data['fight_declare_count'] = Fights::where('fight_declaration','!=',0)->count();

        if( $fight->fight_declaration != 0 ) {

            $replace = [
                'left_side_total_percentage' => '0%',
                'right_side_total_percentage' => '0%',
                'left_user_payout' => '0',
                'right_user_payout' => '0',
                'left_side_total_bet' => '0',
                'right_side_total_bet' => '0'
            ];

            $data = array_replace($data,$replace);
        }
        
        return view('arena.index', ['page_title' => 'Arena','fight' => $fight,'user' => $user,'bet' => (object) $data]);
    }

    public function csc(Request $request) {

        $subjects = ChatSubject::where('user_id',Auth::user()->id)->orderBy('created_at','desc')->paginate(10);

        return view('chats.subject',[
            'subjects' => $subjects,
            'page_title' => 'Customer Service Chat'
        ]);
    }

    public function view_chat(Request $request) {

        $subjects = ChatSubject::where('id',$request->subject_id)->first();

        if(!$subjects) return redirect('/user/ask/');
        if($subjects->status == 1)  return redirect('/user/ask/');

        return view('chats.view' , [
            'subjects' => $subjects,
            'page_title' => 'CSC [' . $subjects->subject_name . ']'
        ]);
    }

    public function load_chat(Request $request) {

        $chats = ChatMessage::where('subject_id',$request->s_id)->orderBy('id','desc')->paginate(20);

        if(!$chats || session()->token() != $request->token) return response()->json(['status' => false],200,[],JSON_UNESCAPED_UNICODE);

        return response()->json(['status' => true,'chats' => $chats],200,[],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    }

    public function send_chat(Request $request) {

        if($request->has('message') && $request->has('subject_id')) {

            ChatMessage::create([
                'user_id' => Auth::user()->id,
                'message' => strip_tags($request->message),
                'sender' => 'USER',
                'subject_id' => $request->subject_id

            ]);

            return response()->json(['status' => true],200,[],JSON_UNESCAPED_UNICODE);

        } else {
            return response()->json(['status' => false],200,[],JSON_UNESCAPED_UNICODE);
        }
    }

    public function create_sub(Request $request) {

        $subject = ChatSubject::create([
            'subject_name' => $request->subject_name,
            'user_id' => Auth::user()->id,
            'hasReply' => 0,
            'status' => 0
        ]);

        ChatMessage::create([
            'user_id' => Auth::user()->id,
            'message' => "Hello, player, how can we help you?",
            'sender' => 'CSR',
            'subject_id' => $subject->id
        ]);

        return redirect()->back();
    }

    public function addbet(Request $request) {

        $fight = Fights::orderBy('id','desc')->skip(0)->take(1)->first();
        $credits = Auth::user()->credits;

        $data = [
            'status' => false,
            'message' => ''
        ];

        if($fight->betting_status == 1) {
            return response()->json(['status' => false,'message' => 'Betting is closed!'],200,[],JSON_UNESCAPED_UNICODE);
        } else if($fight->fight_status == 1) {
            return response()->json(['status' => false,'message' => 'Fight is closed!'],200,[],JSON_UNESCAPED_UNICODE);
        }

        if($request->has('bet_type') && $request->has('amount')) {

            $bet_type = ($request->bet_type == 'meron' ? 0 : 1);

            if($request->amount < $this->min_bet) {
                $data['message'] = 'Minimum amount to bet is ' . $this->min_bet;
            } elseif($request->amount <= $credits) {

                $logic = BettingLogic::where('user_id',Auth::user()->id)
                                       ->where('fight_id',$fight->id)
                                       ->where('side', $bet_type);

                if($logic->count() <= 0) {

                    $has_logic = BettingLogic::create([
                        'amount' => $request->amount,
                        'user_id' => Auth::user()->id, 
                        'fight_id' => $fight->id,
                        'side' => $bet_type
                    ]);

                } else {

                    $has_logic = BettingLogic::where('user_id',Auth::user()->id)
                                              ->where('fight_id' ,$fight->id)
                                              ->where('side', $bet_type)
                                              ->update([
                                                    'amount' => ($request->amount + $logic->first()->amount) 
                                                ]);
                }

                $update_credits_user = User::where('id',Auth::user()->id)
                                            ->update([
                                                'credits' => (Auth::user()->credits - $request->amount)
                                            ]);

                if($has_logic && $update_credits_user) {
                    $data['status'] = true;
                    $data['message'] = 'You successfully bet!';
                } else {
                    $data['message'] = 'Failed to bet! Something\'s wrong';
                }

            } else {
                $data['message'] = 'Your credits is not enough to bet';
            }

        } else {
            $data['message'] = 'Failed to bet! Something\'s wrong';
        }

        return response()->json($data,200,[],JSON_UNESCAPED_UNICODE);
    }

    public function betstatus(Request $request) {

        $fight = Fights::orderBy('id','desc')->skip(0)->take(1)->first();

        if(!$fight) {
            return response()->json(['status' => false,'message' => 'Something\'s wrong'],200,[]);
        }

        DB::table('visitors')->updateOrInsert([
            'ip'=> $request->ip(),
            'username' => Auth::user()->username
        ],[
            'username' => Auth::user()->username,
            'ip' => $request->ip(),
            'lastseen' => time()
        ]);
        
        $data['current_points'] = Auth::user()->credits;
        $data['left_side_total_bet'] = BettingLogic::where('fight_id',$fight->id)->where('side',0)->sum('amount');
        $data['right_side_total_bet'] = BettingLogic::where('fight_id',$fight->id)->where('side',1)->sum('amount');

        $left_side_user_bet = BettingLogic::where('fight_id',$fight->id)->where('user_id',Auth::user()->id)->where('side',0)->sum('amount');
        $right_side_user_bet = BettingLogic::where('fight_id',$fight->id)->where('user_id',Auth::user()->id)->where('side',1)->sum('amount');

        if($data['left_side_total_bet'] && $data['right_side_total_bet']) {
            $left_multiplier = (($data['right_side_total_bet'] * $this->percentage) / $data['left_side_total_bet']) ?? 0;
            $left_multiplier = bcdiv($left_multiplier + 1, 1, 2);
                
            $right_multiplier = (($data['left_side_total_bet'] * $this->percentage) / $data['right_side_total_bet']) ?? 0;
            $right_multiplier = bcdiv($right_multiplier + 1 , 1, 2);
        } else {
            $left_multiplier = 0;
            $right_multiplier = 0;
        }

        $request->session()->put('left_multiplier',$left_multiplier);
        $request->session()->put('right_multiplier',$right_multiplier);

        $data['left_side_total_percentage'] = ( $left_multiplier * 100 ) . '%' ?? '0%';
        $data['right_side_total_percentage'] = ( $right_multiplier * 100 ) . '%' ?? '0%';
        $data['current_fight_no'] = $fight->fight_no;
        $data['left_user_payout'] = bcdiv( ($left_side_user_bet * $left_multiplier),1,2);
        $data['right_user_payout'] = bcdiv( ($right_side_user_bet * $right_multiplier),1,2);

        $data['cant_bet'] = (bool) $fight->betting_status;
        $data['betting_logs'] = BetLogs::where('user_id',Auth::user()->id)->orderBy('logs_date','desc')->take(5)->get();

        $fights = Fights::where('fight_declaration','!=',0)->get();
        $counts = [];
        if($fights) {
            foreach ($fights as $entry) {
                if (!isset($previous)) {
                    $currentCount = ['count' => 1];
                    $currentCount['fight_declaration'] = $entry['fight_declaration'];
                } elseif ($entry['fight_declaration'] === $previous['fight_declaration']) {
                    $currentCount['count']++;
                    $currentCount['fight_declaration'] = $entry['fight_declaration'];
                } else {
                    $counts[] = $currentCount;
                    $currentCount = ['count' => 1];
                    $currentCount['fight_declaration'] = $entry['fight_declaration'];
                }
                $previous = $entry;
            }

            if (isset($currentCount)) {
                $counts[] = $currentCount;
            }
        }
        $data['trending'] = $counts;

        $msg_logs_count = MsgLogs::count();
        $msg_logs_count_session = $request->session()->get('msg_logs_count');

        if($msg_logs_count) {

            if($request->session()->has('msg_logs_count')) {

                if($msg_logs_count_session != $msg_logs_count) {
                    $data['msg_log_status'] = true;
                    $data['msg_log_content'] = MsgLogs::orderBy('id','desc')->skip(0)->take(1)->first()->content;
                    $request->session()->put('msg_logs_count', $msg_logs_count);
                }
                
            } else {
                $request->session()->put('msg_logs_count', $msg_logs_count);
            }

        }
        

        $bet_logs_count = BetLogs::where('user_id',Auth::user()->id)->count();
        $bet_logs_count_session = $request->session()->get('bet_logs_count');

        if($bet_logs_count) {

            if($request->session()->has('bet_logs_count')) {

                if($bet_logs_count_session != $bet_logs_count) {

                    $bet_logs = BetLogs::where('user_id',Auth::user()->id)->orderBy('id','desc')->skip(0)->take(1)->first();
                    
                    $msg_body = "Fight #{$bet_logs->fight_no} <br>";
                    $msg_body .= "Action: {$bet_logs->action} <br>";
                    $msg_body .= "Side: " . ucfirst($bet_logs->side) . "<br>";
                    $msg_body .= "Amount: {$bet_logs->amount} <br>";
                    $msg_body .= "Bet: {$bet_logs->bet} <br>";
                    $msg_body .= "Balance: {$bet_logs->balance}";

                    $data['notif_user_status'] = true;
                    $data['notif_user_msg'] = $msg_body;
                    $request->session()->put('bet_logs_count', $bet_logs_count);
                }
                
            } else {
                $request->session()->put('bet_logs_count', $bet_logs_count);
            }

        }

        $request_logs_count = UserRequest::where('user_id',Auth::user()->id)->count();
        $request_logs_count_session = $request->session()->get('request_logs_count');
        $request_logs = UserRequest::where('user_id',Auth::user()->id)->orderBy('id','desc')->skip(0)->take(1)->first();

        if($request_logs && $request_logs->status != "Pending") {

            if($request_logs_count) {

                if($request->session()->has('request_logs_count')) {

                    if($request_logs_count_session != $request_logs_count) {

                        $msg_body = "Cash Type: " . ucfirst($request_logs->type) . "<br>";

                        if($request_logs->type == 'withdraw') {

                            if($this->hasDeduction) {

                                if($request_logs->status == 'Approved') {
                                    $msg_body .= "Message: {$request_logs->withdraw_msg} <br>";
                                }

                            }
                            
                            $msg_body .= "Amount: {$request_logs->amount} <br>";
                            $msg_body .= "Account Name: {$request_logs->account_name} <br>";
                            $msg_body .= "Account Number: "  . $request_logs->account_number . "<br>";
                            $msg_body .= "Status: {$request_logs->status} <br>";
                            $msg_body .= "Date: " . date('F j Y h:i A', strtotime($request_logs->request_date));

                        } else {

                            $msg_body .= "Amount: {$request_logs->amount} <br>";
                            $msg_body .= "Sender Number: {$request_logs->sender_number} <br>";
                            $msg_body .= "Receiver Number: "  . $request_logs->reciever_number . "<br>";
                            $msg_body .= "Ref No: {$request_logs->reference_number} <br>";
                            $msg_body .= "Status: {$request_logs->status} <br>";
                            $msg_body .= "Date: " . date('F j Y h:i A', strtotime($request_logs->request_date));

                        }

                        $data['request_user_status'] = true;
                        $data['request_user_msg'] = $msg_body;

                        $request->session()->put('request_logs_count', $request_logs_count);
                    }
                    
                } else {
                    $request->session()->put('request_logs_count', $request_logs_count);
                }

            }

        } else {

            $data['request_user_status'] = false;
            $data['request_user_msg'] = "";

        }

        if( $fight->fight_declaration != 0 ) {

            $replace = [
                'left_side_total_percentage' => '0%',
                'right_side_total_percentage' => '0%',
                'left_user_payout' => '0',
                'right_user_payout' => '0',
                'left_side_total_bet' => '0',
                'right_side_total_bet' => '0'
            ];

            $data = array_replace($data,$replace);
        }

        return response()->json($data,200,[],JSON_UNESCAPED_UNICODE);
    }

    public function msglog(Request $request) {
        $logs = MsgLogs::create([
            'content' => $request->response
        ]);

        if( !$logs ) {
            return response()->json(['status' => true,'message' => 'Something\'s wrong'],200,[]);
        }
    }

    public function eventfire(Request $request) {

        $fight = Fights::orderBy('id','desc')->skip(0)->take(1)->first();

        if($request->has('response')) {

            $response = 0;

            if($request->response == 'close') {
                $response = 1;
            }

            if($fight->fight_status == 1) {
                return response()->json(['status' => true,'message' => 'Fight is already closed'],200,[]);
            } else if($fight->betting_status == $response) {
                return response()->json(['status' => true,'message' => 'Betting is already ' . $request->response ],200,[]);
            } else {

                Fights::where('id',$fight->id)->update(['betting_status' => $response ]);

                MsgLogs::create([
                    'content' => 'Betting is now  ' . ($request->response == 'close' ? 'closed' : $request->response)
                ]);
            }

        } else {
            
            return response()->json(['status' => true,'message' => 'Something\'s wrong'],200,[]);

        }

    }

    public function addfight(Request $request) {

        $fight = Fights::orderBy('id','desc')->skip(0)->take(1)->first();
        
        if ($request->has('fight_no')) {

            if($fight->fight_status == 0 && $fight->betting_status == 0) {
                return response()->json(['status' => false,'message' => 'Fight is open, You can\'t add fight now' ],200,[]);
            } else {

                Fights::create(['fight_no' => $request->fight_no]);
                MsgLogs::create([
                    'content' => 'Fight #' . $request->fight_no . ' is now ready!'
                ]);
                
                return response()->json(['status' => true],200,[]);
            }

        } else {
            return response()->json(['status' => false,'message' => 'Something\'s wrong'],200,[]);
        }
    }

    public function declarefight(Request $request) {

        $fight = Fights::orderBy('id','desc')->skip(0)->take(1)->first();

        if($request->has('declare')) {

            $response = [
                'status' => false,
                'message' => ''
            ];

            $declare_data = [
                'meron' => 1,
                'wala' => 2,
                'cancel' => 4,
                'draw' => 3
            ];

            if($fight->betting_status == 0 && $request->declare != 'reopen') {
                return response()->json(['status' => false,'message' => 'Betting is open, Please close it'],200,[]);
            } else if( $fight->fight_status == 1 && $request->declare != 'redeclare' ) {
                return response()->json(['status' => false,'message' => 'Fight is already closed!'],200,[]);
            } else if(!in_array($request->declare,['reopen','redeclare']) && $fight->fight_declaration == $declare_data[ $request->declare ]) {
                return response()->json(['status' => false,'message' => 'Fight already declared!'],200,[]);
            } else if($fight->betting_status == 0 && $request->declare == 'reopen') {
                return response()->json(['status' => false,'message' => 'Betting is open, you can\'t reopen it!'],200,[]);
            } else if($fight->fight_status == 0 && $request->declare == 'redeclare') {
                return response()->json(['status' => false,'message' => 'Fight is open, you can\'t redeclare it!'],200,[]);
            }

            if( $request->declare == 'meron' || $request->declare == 'wala' ) {
                $this->go_declare_side_win($request->declare,$response,$fight,$declare_data);
                $this->go_declare_side_lost($request->declare,$fight,$declare_data);
            } else if( $request->declare == 'draw' || $request->declare == 'cancel' ) {
                $this->go_declare_side_neutral($request->declare,$response,$fight,$declare_data);
            } else if( $request->declare == 'reopen' || $request->declare == 'redeclare') {
                $this->go_declare_side_special_func($request->declare,$response,$fight,$declare_data);
            }

            return response()->json($response,200,[]);

        } else {
            return response()->json(['status' => false,'message' => 'Something\'s wrong'],200,[]);
        }

    }

    public function resetevent(Request $request) {

        $delete = Fights::query()->delete();
        $delete_logic = BettingLogic::query()->delete();
        $insert = Fights::create([
            'betting_status' => 0,
            'fight_status' => 0,
            'fight_declaration' => 0,
            'fight_no' => 1
        ]);

        if(!$delete && !$insert) {
            return response()->json(['status' => false,'message' => 'Can\'t reset event right now!'],200,[]);
        }

        return response()->json(['status' => true,'message' => 'Successfully, event has resetted!'],200,[]);

    }

    /**
     * Declare redeclare/reopen
     *
     * @return void
     * @param $declare 
     * @param $response (Pass by reference)
     */
    function go_declare_side_special_func($declare , &$response,$fight,$declare_data) {

        $word_dec = ucfirst($declare);
        $bet_logic = BettingLogic::where('fight_id',$fight->id)->get();

        if( $bet_logic ) {

            if($declare == 'reopen') {

                Fights::where('id',$fight->id)->update(
                    [
                        'betting_status' => 0
                    ]
                );

                foreach($bet_logic as $logic) {
                    DB::table('users')->where( ['id' => $logic->user_id] )->update(['credits' => DB::raw('credits + ' . $logic->amount) ]);
                }

                BettingLogic::where('fight_id',$fight->id)->delete();

            } else {

                Fights::where('id',$fight->id)->update(
                    [
                        'fight_status' => 0,
                        'fight_declaration' => 0
                    ]
                );

                $logs_logic = BetLogs::where('fight_id',$fight->id)->where('action','!=','Lost')->get();

                foreach($logs_logic as $logic) {
                    DB::table('users')->where( ['id' => $logic->user_id] )->update(['credits' => DB::raw('credits - ' . $logic->amount) ]);
                }

                BetLogs::where('fight_id',$fight->id)->delete();
                SiteLogs::where('fight_id',$fight->id)->delete();
            }

            MsgLogs::create([
                'content' => 'Fight #' . $fight->fight_no . ' has been ' . ($declare == 'redeclare' ? 'redeclared' : 'reopen')
            ]);

            $response['status'] = true;
            $response['message'] = 'Successfully declared, selected ' . $word_dec;

        } else {
            $response['status'] = false;
            $response['message'] = 'Something\'s wrong with the system!';
        }
    }


    /**
     * Declare Cancel/Draw
     *
     * @return void
     * @param $declare 
     * @param $response (Pass by reference)
     */

    function go_declare_side_neutral($declare , &$response,$fight,$declare_data) {
        
        $word_dec = ucfirst($declare);
        $bet_logic = BettingLogic::where('fight_id',$fight->id)->get();

        if( $bet_logic ) {

            Fights::where('id',$fight->id)->update(
                [
                    'fight_status' => 1,
                    'fight_declaration' => $declare_data[$declare]
                ]
            );

            foreach($bet_logic as $logic) {

                DB::table('users')->where( ['id' => $logic->user_id] )->update(['credits' => DB::raw('credits + ' . $logic->amount) ]);

                BetLogs::create([
                    'user_id' => $logic->user_id,
                    'fight_no' => $fight->fight_no,
                    'fight_id' => $fight->id,
                    'amount' => $logic->amount,
                    'side' => 'Meron/Wala',
                    'bet' => $logic->amount,
                    'action' => $declare,
                    'balance' => DB::table('users')->select('credits')->where( ['id' => $logic->user_id] )->first()->credits
                ]);

            }

            MsgLogs::create([
                'content' => 'Battle for fight #' . $fight->fight_no . ' is ' . $declare . ', All amounts have been return to the players'
            ]);

            $response['status'] = true;
            $response['message'] = 'Successfully declared, ' . $declare . ' fight #' . $fight->fight_no;

        } else {
            $response['status'] = false;
            $response['message'] = 'Something\'s wrong with the system!';
        }
    }


    /**
     * Declare Winner
     *
     * @return void
     * @param $declare 
     * @param $response (Pass by reference)
     */
    function go_declare_side_win($declare , &$response,$fight,$declare_data) {

        $word_dec = ucfirst($declare);
        
        if($declare == 'meron') $bet_side = 0;
        else if($declare == 'wala') $bet_side = 1;

        $bet_logic = BettingLogic::where('fight_id',$fight->id)->where('side',$bet_side)->get();
        $multiplier = [
            'meron' => session()->get('left_multiplier'),
            'wala' => session()->get('right_multiplier')
        ];

        if( $bet_logic ) {

            $total_bet = BettingLogic::where('fight_id',$fight->id)->sum('amount');
            $bet = 0;

            Fights::where('id',$fight->id)->update(
                [
                    'fight_status' => 1,
                    'fight_declaration' => $declare_data[$declare]
                ]
            );

            foreach( $bet_logic as $logic ) {

                $payout_amount = bcdiv( ($multiplier[$declare] * $logic->amount) * $this->payout , 1 ,2 );

                $agent_payout = [
                    'master' => bcdiv( ($multiplier[$declare] * $logic->amount) * $this->master_agent_commission , 1 , 2 ) , 
                    'agent' => bcdiv( ($multiplier[$declare] * $logic->amount) * $this->agent_commission , 1 , 2 ) ,
                    'site' => bcdiv( ($multiplier[$declare] * $logic->amount) * $this->site_commission , 1 , 2 )
                ];

                $user = DB::table('users')->select('*')->where( ['id' => $logic->user_id] )->first();
                $user_update = DB::table('users')->where( ['id' => $logic->user_id] )->update(['credits' => ($user->credits + $payout_amount) ]);

                if(!$user_update) {
                    $response['status'] = false;
                    $response['message'] = 'Something\'s wrong (with user)';
                } else {

                    if($user->referral_code == '5912') {

                        SiteLogs::create([
                            'amount' => $agent_payout['site'],
                            'fight_id' => $fight->id
                        ]);

                    } else {

                        $referral = Referral::where('code',$user->referral_code)->first();

                        if($referral->master_agent_id == 0) {
                           
                            DB::table('users')->where( ['id' => $referral->agent_id] )->update( ['credits' => DB::raw('credits + ' . $agent_payout['agent'] ) ] );

                            BetLogs::create([
                                'user_id' => $referral->agent_id,
                                'fight_no' => $fight->fight_no,
                                'fight_id' => $fight->id,
                                'amount' => $agent_payout['agent'],
                                'side' => $declare,
                                'bet' => 0,
                                'action' => 'Commission',
                                'balance' => DB::table('users')->select('credits')->where( ['id' => $referral->agent_id ] )->first()->credits
                            ]);        

                            SiteLogs::create([
                                'amount' => $agent_payout['master'],
                                'fight_id' => $fight->id
                            ]);
                

                        } elseif($referral->agent_id == 0) {

                            if($this->hasReversedAgent) {

                               $reversed_amount = abs( $agent_payout['master'] + $agent_payout['agent'] );

                            } else {

                               $reversed_amount = $agent_payout['master'];

                            }

                            DB::table('users')->where( ['id' => $referral->master_agent_id] )->update( ['credits' => DB::raw('credits + ' . $reversed_amount ) ] );

                            BetLogs::create([
                                'user_id' => $referral->master_agent_id,
                                'fight_no' => $fight->fight_no,
                                'fight_id' => $fight->id,
                                'amount' => $reversed_amount,
                                'side' => $declare,
                                'bet' => 0,
                                'action' => 'Commission',
                                'balance' => DB::table('users')->select('credits')->where( ['id' => $referral->master_agent_id ] )->first()->credits
                            ]);        


                            if(!$this->hasReversedAgent) {

                                SiteLogs::create([
                                    'amount' => $agent_payout['agent'],
                                    'fight_id' => $fight->id
                                ]);    

                            }


                        } else {

                            DB::table('users')->where( ['id' => $referral->agent_id] )->update( ['credits' => DB::raw('credits + ' . $agent_payout['agent'] ) ] );

                            BetLogs::create([
                                'user_id' => $referral->agent_id,
                                'fight_no' => $fight->fight_no,
                                'fight_id' => $fight->id,
                                'amount' => $agent_payout['agent'],
                                'side' => $declare,
                                'bet' => 0,
                                'action' => 'Commission',
                                'balance' => DB::table('users')->select('credits')->where( ['id' => $referral->agent_id ] )->first()->credits
                            ]);        
                           
                            DB::table('users')->where( ['id' => $referral->master_agent_id] )->update( ['credits' => DB::raw('credits + ' . $agent_payout['master'] ) ] );

                            BetLogs::create([
                                'user_id' => $referral->master_agent_id,
                                'fight_no' => $fight->fight_no,
                                'fight_id' => $fight->id,
                                'amount' => $agent_payout['master'],
                                'side' => $declare,
                                'bet' => 0,
                                'action' => 'Commission',
                                'balance' => DB::table('users')->select('credits')->where( ['id' => $referral->master_agent_id ] )->first()->credits
                            ]);        
                        }


                    }

                    BetLogs::create([
                        'user_id' => $logic->user_id,
                        'fight_no' => $fight->fight_no,
                        'fight_id' => $fight->id,
                        'amount' => $payout_amount,
                        'side' => $declare,
                        'bet' => $logic->amount,
                        'action' => 'Win',
                        'balance' => DB::table('users')->select('credits')->where( ['id' => $logic->user_id] )->first()->credits
                    ]);

                }

                // Add all amount to
                // bet variable
                $bet += $logic->amount;

            }

            $site_commission_amount = abs($total_bet - ($bet * $multiplier[$declare] ) );

            SiteLogs::create([
                'amount' => $site_commission_amount,
                'fight_id' => $fight->id
            ]);

            MsgLogs::create([
                'content' => $word_dec . ' win\'s, Congratulations to the winners'
            ]);

            $response['status'] = true;
            $response['message'] = 'Successfully declared, ' . $word_dec . ' as winner';

        } else {

            $response['status'] = false;
            $response['message'] = 'You declare this fight, betting is empty!, declare as cancel fight!';

        }

    }

    /**
     * Declare Lose
     *
     * @return void
     * @param $declare 
     * @param $response (Pass by reference)
     */
    function go_declare_side_lost($declare ,$fight,$declare_data) {

        $word_dec = ucfirst($declare);
        
        if($declare == 'meron') {
            $bet_side = 1;
            $declare = 'wala';
        } else if($declare == 'wala') {
            $bet_side = 0;
            $declare = 'meron';
        }

        $bet_logic = BettingLogic::where('fight_id',$fight->id)->where('side',$bet_side)->get();

        if( $bet_logic ) {

            foreach( $bet_logic as $logic ) {

                $agent_payout = [
                    'master' => bcdiv( ($logic->amount) * $this->master_agent_commission , 1 , 2 ) , 
                    'agent' => bcdiv( ($logic->amount) * $this->agent_commission , 1 , 2 ) ,
                    'site' => bcdiv( ($logic->amount) * $this->site_commission , 1 , 2 )
                ];

                $user = DB::table('users')->select('*')->where( ['id' => $logic->user_id] )->first();

                if($user->referral_code == '5912') {

                    SiteLogs::create([
                        'amount' => $agent_payout['site']
                    ]);

                } else {

                    $referral = Referral::where('code',$user->referral_code)->first();

                    if($referral->master_agent_id == 0) {
                    
                        DB::table('users')->where( ['id' => $referral->agent_id] )->update( ['credits' => DB::raw('credits + ' . $agent_payout['agent'] ) ] );

                        BetLogs::create([
                            'user_id' => $referral->agent_id,
                            'fight_no' => $fight->fight_no,
                            'fight_id' => $fight->id,
                            'amount' => $agent_payout['agent'],
                            'side' => $declare,
                            'bet' => 0,
                            'action' => 'Commission',
                            'balance' => DB::table('users')->select('credits')->where( ['id' => $referral->agent_id ] )->first()->credits
                        ]);        

                        SiteLogs::create([
                            'amount' => $agent_payout['master'],
                            'fight_id' => $fight->id
                        ]);

                    } elseif($referral->agent_id == 0) {

                        if($this->hasReversedAgent) {

                            $reversed_amount = abs( $agent_payout['master'] + $agent_payout['agent'] );

                         } else {

                            $reversed_amount = $agent_payout['master'];

                         }

                         DB::table('users')->where( ['id' => $referral->master_agent_id] )->update( ['credits' => DB::raw('credits + ' . $reversed_amount ) ] );

                         BetLogs::create([
                             'user_id' => $referral->master_agent_id,
                             'fight_no' => $fight->fight_no,
                             'fight_id' => $fight->id,
                             'amount' => $reversed_amount,
                             'side' => $declare,
                             'bet' => 0,
                             'action' => 'Commission',
                             'balance' => DB::table('users')->select('credits')->where( ['id' => $referral->master_agent_id ] )->first()->credits
                         ]);        


                         if(!$this->hasReversedAgent) {

                             SiteLogs::create([
                                 'amount' => $agent_payout['agent'],
                                 'fight_id' => $fight->id
                             ]);    

                         }


                    } else {

                        DB::table('users')->where( ['id' => $referral->agent_id] )->update( ['credits' => DB::raw('credits + ' . $agent_payout['agent'] ) ] );

                        BetLogs::create([
                            'user_id' => $referral->agent_id,
                            'fight_no' => $fight->fight_no,
                            'fight_id' => $fight->id,
                            'amount' => $agent_payout['agent'],
                            'side' => $declare,
                            'bet' => 0,
                            'action' => 'Commission',
                            'balance' => DB::table('users')->select('credits')->where( ['id' => $referral->agent_id ] )->first()->credits
                        ]);        
                    
                        DB::table('users')->where( ['id' => $referral->master_agent_id] )->update( ['credits' => DB::raw('credits + ' . $agent_payout['master'] ) ] );

                        BetLogs::create([
                            'user_id' => $referral->master_agent_id,
                            'fight_no' => $fight->fight_no,
                            'fight_id' => $fight->id,
                            'amount' => $agent_payout['master'],
                            'side' => $declare,
                            'bet' => 0,
                            'action' => 'Commission',
                            'balance' => DB::table('users')->select('credits')->where( ['id' => $referral->master_agent_id ] )->first()->credits
                        ]);        
                    }


                }

                BetLogs::create([
                    'user_id' => $logic->user_id,
                    'fight_no' => $fight->fight_no,
                    'fight_id' => $fight->id,
                    'amount' => $logic->amount,
                    'side' => $declare,
                    'bet' => $logic->amount,
                    'action' => 'Lost',
                    'balance' => DB::table('users')->select('credits')->where( ['id' => $logic->user_id] )->first()->credits
                ]);

            }

        }

    }



}
