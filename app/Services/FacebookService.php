<?php

namespace App\Services;

use \Facebook\Facebook;
use GuzzleHttp;

class FaceBookService
{
    public function client($accessToken)
    {
        $client = new \Facebook\Facebook([
          'app_id' => config('services.facebook.client_id'),
          'app_secret' => config('services.facebook.client_secret'),
          'default_graph_version' => 'v2.8',
          'default_access_token' => $accessToken
        ]);

        return $client;
    }

    public function getRefreshedToken($currentAccessToken = null)
    {
        try {
            $client = new GuzzleHttp\Client();
            $params = [
                'grant_type' => 'fb_exchange_token',
                'client_id' => config('services.facebook.client_id'),
                'client_secret' => config('services.facebook.client_secret'),
                'fb_exchange_token' => $currentAccessToken
            ];

            $longLivedTokenResponse = $client->request('GET', 'https://graph.facebook.com/v2.5/oauth/access_token', [
                'query' => $params
            ]);

            $response = json_decode($longLivedTokenResponse->getBody(), true);
            return $response['access_token'];
        } catch(Exception $e) {
            return ['error' => 'Token refresh unsuccessful.'];
        }
    }
}