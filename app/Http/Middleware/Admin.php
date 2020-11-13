<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if( !in_array(Auth::user()->user_role, [ 3 , 4 ]) ) {
            return response()->json(['status' => true,'message' => 'Access Denied'],200,[]);
        }
        
        return $next($request);
    }
}
