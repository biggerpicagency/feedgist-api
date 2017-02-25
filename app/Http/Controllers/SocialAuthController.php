<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp;
use App\Services\UserService;

class SocialAuthController extends Controller
{
    public function callback(Request $request, UserService $userService)
    {
        $accessToken = $request->input('accessToken');

        if (!$accessToken) {
            $client = new GuzzleHttp\Client();
            $params = [
                'code' => $request->input('code'),
                'client_id' => $request->input('clientId'),
                'redirect_uri' => $request->input('redirectUri'),
                'client_secret' => config('services.facebook.client_secret')
            ];

            $accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/v2.5/oauth/access_token', [
                'query' => $params
            ]);
            $response = json_decode($accessTokenResponse->getBody(), true);
            $accessToken = $response['access_token'];
        }

        return $this->apiResponse( $userService->authenticateFacebookUser($accessToken) );
    }
}
