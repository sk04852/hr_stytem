<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AutoGenerateURL;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

class VerifyUrl
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
        $url =  AutoGenerateURL::where('token', $request->token)->first();
        if(!is_null($url) && $url->status == 'valid'){
            return $next($request);
        }

        throw new InvalidSignatureException;
    }
}
