<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Controllers\Clients\Models\Client;

class ClientTokenValidityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        global $app;
        if(!empty($request->bearerToken()) || !empty($request->get('token'))) {
            $token = (empty($request->bearerToken()))? $request->get('token'): $request->bearerToken();
            $client = Client::where([
                ['token', $token],
                ['expires_at', '>=', date("Y-m-d")],
            ])->first();
            if (!$client->exists()) {
                return response()->json(['message' => 'Invalid token'], 401);
            }
            $app['client'] = $client;
        } else {
            return response()->json(['message' => 'Not a valid API request.'], 401);
        }
        return $next($request);
    }
}
