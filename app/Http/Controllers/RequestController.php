<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RandNumbers;
use App\Models\UserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RequestController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function request(Request $request)
    {
        $accounts = RandNumbers::get();

        return view('request.request',['page_title' => 'My Requests', 'accounts' => $accounts,'req_type' => $request->type]);
    }

    public function submit(Request $request) {
        
        if($request->has('req_type') ) {

            if($request->req_type == 'deposit') {

                foreach($request->post() as $post => $value) {
                    if($post != '_token' && $post != 'req_type' && $post != 'screenshot') 
                        $data[$post] = $value;
                    
                    if($post == 'req_type')
                        $data['type'] = $value;
                }
               
                $data['user_id'] = Auth::user()->id;
                $filename = time()  . '_' . $request->screenshot->getClientOriginalName();
                $request->screenshot->storeAs('requests',$filename );
                $data['screenshot_path'] = Storage::url("requests/{$filename}");
                $data['status'] = 'Pending';
                $req_update = UserRequest::create($data);

                if(!$req_update) return redirect()->back()->withErrors(['message'=> 'Failed to submit your request']); 

            } else if($request->req_type == 'withdraw') {

                foreach($request->post() as $post => $value) {
                    if($post != '_token' && $post != 'req_type') 
                        $data[$post] = $value;
                    
                    if($post == 'req_type')
                        $data['type'] = $value;
                }
               
                $data['user_id'] = Auth::user()->id;
                $data['status'] = 'Pending';
                $req_update = UserRequest::create($data);

                if(!$req_update) return redirect()->back()->withErrors(['message'=> 'Failed to submit your request']); 

            } else {
                return redirect()->back()->withErrors(['message'=> 'Failed! Action is not defined']);
            }

        } else {
            return redirect()->back()->withErrors(['message'=> 'Failed! Action is not defined']);
        }

        return redirect()->back()->withErrors(['message'=> 'Successfully to submit your request']);
    }

    public function history(Request $request) {

        $requests = UserRequest::where('user_id',Auth::user()->id)->where('type',$request->type)->orderBy('request_date','desc')->paginate(10);

        return view('request.history',['histories' => $requests,'request_type' => $request->type,'page_title' => 'My Request History']);
    }

}
