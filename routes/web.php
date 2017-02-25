<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', 'HomeController@index');
Route::get('/likedPages', function() {
    $userId = '1359629890724947';
    $accessToken = 'EAAQUeHsr6tQBADZAiQldAmjWTui9LEnA3tZCqRoXRt1oGg8Nw2rWinwvD3lkvCHketBFvWwwIOfogTu0SStruLQrvjgMUwevJv28Y0s6G63DFCHZAhxJjZCDgMcvBuFdfBjsxxU1uKCZCoYvpEDckilcH6MAZAtpQZD';

    $fb = new \Facebook\Facebook([
      'app_id' => config('services.facebook.client_id'),
      'app_secret' => config('services.facebook.client_secret'),
      'default_graph_version' => 'v2.8'
    ]);


    try {
      // Get the \Facebook\GraphNodes\GraphUser object for the current user.
      // If you provided a 'default_access_token', the '{access-token}' is optional.
      $response = $fb->get('/' . $userId . '/likes?limit=100&fields=name,picture,category', $accessToken);
    } catch(\Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(\Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }



    $graphEdge = $response->getGraphList();

    $totalLikes = [];
    $likesArray = [];


    $likesArray = $graphEdge->asArray();
    $totalLikes = array_merge($totalLikes, $likesArray);
    $categorisedLikes = [];


    dd($totalLikes);


    if ($fb->next($graphEdge)) {  

        $likesArray = $graphEdge->asArray();
        $lc = count($likesArray);
        $totalLikes = array_merge($totalLikes, $likesArray); 

        while ($graphEdge = $fb->next($graphEdge)) { 
            $likesArray = $graphEdge->asArray();
            $lic = count($likesArray);
            $totalLikes = array_merge($totalLikes, $likesArray);
        }

    } else {
        $likesArray = $graphEdge->asArray();
        $totalLikes = array_merge($totalLikes, $likesArray);
    }

    dd([$totalLikes]);


/*
    $pageId = '152359208112754';
    try {
      // Get the \Facebook\GraphNodes\GraphUser object for the current user.
      // If you provided a 'default_access_token', the '{access-token}' is optional.
      $response = $fb->get('/' . $pageId . '/posts', $accessToken);
    } catch(\Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      exit;
    } catch(\Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      exit;
    }

    $graphEdge = $response->getGraphList();

    dd($graphEdge);*/
});