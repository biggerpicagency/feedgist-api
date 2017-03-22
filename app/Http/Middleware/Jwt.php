<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\User;

class Jwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = $this->getUser();

            if (!$user) {
                return response()->json(['error' => 'User not authenticated.'], 401);
            } else {
                $request->merge(['user' => $user]);
            }

        } catch (TokenExpiredException $e) {
            $token = JWTAuth::getToken();
            $newToken = JWTAuth::refresh($token);
            $user = JWTAuth::setToken($newToken)->toUser();

            $request->merge(['token' => $newToken, 'user' => $user]);
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return $next($request);
    }

    private function getUser()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return User::where('id', $user['id'])->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}
