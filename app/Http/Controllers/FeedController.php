<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FeedService;

class FeedController extends Controller
{
    public function settings(FeedService $feedService)
    {
        return $this->apiResponse( $feedService->getSettings() );
    }
}
