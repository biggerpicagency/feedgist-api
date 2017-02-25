<?php

namespace App\Services;

use App\User;
use JWTAuth;

class UserService
{
    protected $facebook;

    public function __construct(FacebookService $facebook)
    {
        $this->facebook = $facebook;
    }

    public function authenticateFacebookUser($token = null) {
        if (empty($token)) {
            return ['error' => 'Access Token not provided.'];
        }

        $token = $this->facebook->getRefreshedToken($token);
        $client = $this->facebook->client($token);

        try {
            $response = $client->get('/me?fields=name,email');
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return ['error' => 'Graph returned an error: ' . $e->getMessage()];
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return ['error' => 'Facebook SDK returned an error: ' . $e->getMessage()];
        }

        return $this->findOrCreateUser($response->getGraphUser(), $token);
    }

    private function findOrCreateUser($facebookGraphUser, $token = null)
    {
        $user = User::where('social_id', '=', $facebookGraphUser['id'])->first();

        if (is_object($user)) {
            $user->token = $token;
            $user->save();

            return $this->getTokenFromUser($user);
        } else {
            $result = array();
            $result['name'] = $facebookGraphUser['name'];
            $result['email'] = $facebookGraphUser['email'];
            $result['social_id'] = $facebookGraphUser['id'];
            $result['token'] = $token;
            $result['password'] = '';

            try {
                $user = User::create($result);
            } catch (Exception $e) {
                return ['error' => 'User already exists.'];
            }

            return $this->getTokenFromUser($user);
        }
    }

    private function getTokenFromUser($user)
    {
        $token = JWTAuth::fromUser($user);
        return ['token' => $token];
    }
}