<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BetLogs;
use Illuminate\Support\Facades\Auth;

class BettingLogsController extends Controller
{

    public function __construct()
    {
        $this->middleware('revalidate');
        $this->middleware('auth');
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request)
    {

        $transactions = BetLogs::where('user_id', Auth::user()->id)->orderBy('logs_date','desc')->paginate(10);

        return view('betting_logs.index',['page_title' => 'My Betting Logs','logs' => $transactions]);
    }
}
