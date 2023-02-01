<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ValidToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!Auth::guard('api')->check()) {
            return response()->json(['message' => 'Usuário não autenticado'], 401);
        }

        if (!$token) {
            return response()->json(['message' => 'Token Inválido'], 401);
        }

        $user = Auth::guard('api')->user();
        $person = $user->person;
        # verificao se o usuario está bloqueado.
        if (!$user->active) {
            return response()->json(['message' => 'usuário bloqueado!!'], 401);
        }
        if (!$person->approved_at) {
            return response()->json(['message' => 'seu usuário está aguardando aprovação!!'], 401);
        }
        return $next($request);
    }
}
