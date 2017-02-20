<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Socialite;
use JWTAuth;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }   

    public function callback()
    {
        try {
            $provider = Socialite::with('facebook');

            if ($request->has('code')) {
                $user = $provider->stateless()->user();
            }
        } catch (Exception $e) {
            return redirect('auth/facebook');
        }

        return $this->findOrCreateUser($user);
    }

    private function findOrCreateUser($facebookUser)
    {
        $user = User::where('social_id', '=', $facebookUser->id)->first();

        if (is_object($user)) {
            $token = JWTAuth::fromUser($user);
            return ['wielkie' => 'jol'];
        } else {
            $result = array();
            $result['name'] = $facebookUser->user['first_name']
            $result['email'] = $facebookUser->user['email'];
            $result['social_id'] = $facebookUser->id;

            try {
                $user = User::create($result);
            } catch (Exception $e) {
                return response()->json(['error' => 'User already exists.'], 400);
            }

            $token = JWTAuth::fromUser($user);
            return ['wielkie' => 'jol2'];
        }
    }
}
