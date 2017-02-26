<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FeedService;
use App\Http\Requests\FeedSaveSettings;

class FeedController extends Controller
{
    public function feedList(FeedService $feedService)
    {
        return $this->apiResponse( $feedService->getList() );
    }

    public function settings(FeedService $feedService)
    {
        return $this->apiResponse( $feedService->getSettings() );
    }

    public function saveSettings(FeedSaveSettings $request, FeedService $feedService)
    {
        return $this->apiResponse( $feedService->saveSettings($request) );
    }
}
