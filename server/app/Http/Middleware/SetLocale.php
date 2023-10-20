<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\InvalidLocale;

class SetLocale
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

        $available_locale = config('translatable.locales');
        if(!is_null($request->query('locale'))){
            if(in_array($request->query('locale'), $available_locale)){
                app()->setLocale($request->query('locale'));
            }else{
                throw new InvalidLocale('Invalid Locale');
            }
        }
        return $next($request);
    }
}
