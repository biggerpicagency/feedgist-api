<?php

namespace App\Services;

use App\User;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use JWTAuth;
use App\Models\UsersPages;

class UserService
{
    protected $facebook;

    public function __construct(FacebookService $facebook)
    {
        $this->facebook = $facebook;
    }

    public function authenticateFacebookUser($accessToken = null) {
        if (empty($accessToken)) {
            return ['error' => 'Access Token not provided.'];
        }

        $accessToken = $this->facebook->getRefreshedToken($accessToken);
        $client = $this->facebook->client($accessToken);

        try {
            $response = $client->get('/me?fields=name,email');
        } catch(FacebookResponseException $e) {
            return ['error' => 'Graph returned an error: ' . $e->getMessage()];
        } catch(FacebookSDKException $e) {
            return ['error' => 'Facebook SDK returned an error: ' . $e->getMessage()];
        }

        return $this->findOrCreateUser($response->getGraphUser(), $accessToken);
    }

    private function findOrCreateUser($facebookGraphUser, $accessToken = null)
    {
        $user = User::where('social_id', $facebookGraphUser['id'])->first();

        if (is_object($user)) {
            $user->token = $accessToken;
            $user->save();

            return $this->returnTokenFromUser($user);
        } else {
            $result = array();
            $result['name'] = $facebookGraphUser['name'];
            $result['email'] = $facebookGraphUser['email'];
            $result['social_id'] = $facebookGraphUser['id'];
            $result['token'] = $accessToken;
            $result['password'] = '';

            try {
                $user = User::create($result);
            } catch (\Exception $e) {
                return ['error' => 'User already exists.'];
            }

            return $this->returnTokenFromUser($user);
        }
    }

    private function returnTokenFromUser($user)
    {
        $userPagesCounter = UsersPages::where('user_id', $user->id)->count();
        $token = JWTAuth::fromUser($user);
        return ['token' => $token, 'pagesCounter' => $userPagesCounter];
    }
}