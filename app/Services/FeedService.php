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
        $totalPages = [];
        $pagesArray = [];

        $pagesArray = $graphEdge->asArray();
        $totalPages = array_merge($totalPages, $pagesArray);
        $categorisedPages = array_merge($categorisedPages, $this->categorisePages($categorisedPages, $pagesArray));

        if ($client->next($graphEdge)) {  
            $pagesArray = $graphEdge->asArray();
            $totalPages = array_merge($totalPages, $pagesArray);
            $categorisedPages = array_merge($categorisedPages, $this->categorisePages($categorisedPages, $pagesArray));

            while ($graphEdge = $client->next($graphEdge)) { 
                $pagesArray = $graphEdge->asArray();
                $totalPages = array_merge($totalPages, $pagesArray);
                $categorisedPages = array_merge($categorisedPages, $this->categorisePages($categorisedPages, $pagesArray));
            }

        } else {
            $pagesArray = $graphEdge->asArray();
            $totalPages = array_merge($totalPages, $pagesArray);
            $categorisedPages = array_merge($categorisedPages, $this->categorisePages($categorisedPages, $pagesArray));
        }

        return ['categories' => $this->finalCategories($categorisedPages), 'all' => $totalPages];
    }

    private function categorisePages($categorisedPages, $pages)
    {
        foreach ($pages as $page) {
            if (!in_array($page['category'], ['Actor', 'Artist', 'Bar', 'Business Service', 'Cars', 'Company', 'Education', 'Entertainment Website', 'Games/Toys', 'Magazine', 'Musician/Band', 'News/Media Website', 'Personal Blog', 'Politician', 'TV Channel', 'Website'])) {
                continue;
            }

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

    private function finalCategories($categories = [])
    {
        $finalArray = [];
        foreach ($categories as $category) {
            $pages = [];
            $category['pages'] = array_unique($category['pages'], SORT_REGULAR);

            foreach ($category['pages'] as $page) {
                $pages[] = $page;
            }

            $category['pages'] = $pages;

            $finalArray[] = $category;
        }

        return $finalArray;
    }
}