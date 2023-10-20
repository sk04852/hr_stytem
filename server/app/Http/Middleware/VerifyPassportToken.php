<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Http\Response as ResponseCode;
use Illuminate\Support\Facades\Route;

class VerifyPassportToken
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
        if(Auth::guard('api')->check()){
            return $next($request);
        }else{
            return response()->json(['error' => 'Authentication Token is Invalid', 'token_status' => 'token_invalid'], ResponseCode::HTTP_UNAUTHORIZED);
        }
    }
}
