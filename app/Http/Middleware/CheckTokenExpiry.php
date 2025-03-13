<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $tokenData = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $token))
            ->first();

        if (!$tokenData) {
            return response()->json(['message' => 'Invalid Token'], Response::HTTP_UNAUTHORIZED);
        }

        // Check expiry
        if ($tokenData->expires_at && Carbon::parse($tokenData->expires_at)->isPast()) {
            return response()->json(['message' => 'Token expired'], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
