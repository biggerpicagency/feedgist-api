<?php

namespace App\Services;

use App\Services\BaseService;
use App\Services\FacebookService;
use Illuminate\Http\Request;

class FeedService extends BaseService
{
    protected $facebookService;

    public function __construct(Request $request, FacebookService $facebookService)
    {
        parent::__construct($request);
        $this->facebookService = $facebookService;
    }

    public function getSettings()
    {
        $client = $this->facebookService->client( $this->getUser()['token'] );
        $response = $client->get('/' . $this->getUser()['social_id'] . '/likes?fields=name,picture,category');
        $graphEdge = $response->getGraphList();

        $categorisedPages = [];
        $totalLikes = [];
        $likesArray = [];

        $likesArray = $graphEdge->asArray();
        $totalLikes = array_merge($totalLikes, $likesArray);
        $categorisedPages = array_merge($categorisedPages, $this->categorisePages($categorisedPages, $likesArray));

        if ($client->next($graphEdge)) {  
            $likesArray = $graphEdge->asArray();
            $totalLikes = array_merge($totalLikes, $likesArray);
            $categorisedPages = array_merge($categorisedPages, $this->categorisePages($categorisedPages, $likesArray));

            while ($graphEdge = $client->next($graphEdge)) { 
                $likesArray = $graphEdge->asArray();
                $totalLikes = array_merge($totalLikes, $likesArray);
                $categorisedPages = array_merge($categorisedPages, $this->categorisePages($categorisedPages, $likesArray));
            }

        } else {
            $likesArray = $graphEdge->asArray();
            $totalLikes = array_merge($totalLikes, $likesArray);
            $categorisedPages = array_merge($categorisedPages, $this->categorisePages($categorisedPages, $likesArray));
        }

        return $categorisedPages;
    }

    private function categorisePages($categorisedPages, $pages)
    {
        foreach ($pages as $page) {
            if (isset($categorisedPages[ $page['category'] ])) {
                $categorisedPages[ $page['category'] ]['pages'][] = $page;

            } else {
                $categorisedPages[ $page['category'] ] = [
                    'name' => $page['category'],
                    'pages' => [$page]
                ];
            }
        }

        return $categorisedPages;
    }
}