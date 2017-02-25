<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp;
use App\Services\UserService;

class SocialAuthController extends Controller
{
    public function callback(Request $request, UserService $userService)
    {
        $client = new GuzzleHttp\Client();
        $accessToken = $request->input('accessToken');

        if (!$accessToken) {
            $params = [
                'code' => $request->input('code'),
                'client_id' => $request->input('clientId'),
                'redirect_uri' => $request->input('redirectUri'),
                'client_secret' => config('services.facebook.client_secret')
            ];
            // Step 1. Exchange authorization code for access token.
            $accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/v2.5/oauth/access_token', [
                'query' => $params
            ]);
            $accessToken = json_decode($accessTokenResponse->getBody(), true);
            $accessToken = $accessToken['access_token'];
        }

        return $this->apiResponse( $userService->authenticateFacebookUser($accessToken) );
    }
}
