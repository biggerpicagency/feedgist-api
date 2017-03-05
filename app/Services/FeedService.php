<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Services\FacebookService;
use App\Http\Requests\FeedSaveSettings;
use App\Models\UsersPages;

class FeedService extends BaseService
{
    protected $facebookService;

    public function __construct(Request $request, FacebookService $facebookService)
    {
        parent::__construct($request);
        $this->facebookService = $facebookService;
    }

    public function getList()
    {
        $pages = UsersPages::where('user_id', $this->getUser()['id'])->get(['page_id'])->pluck('page_id')->toArray();
        $pagesListWithCommas = implode(',', $pages);
        $list = [];

        if (empty($pagesListWithCommas)) {
            return ['list' => $list];
        }

        $client = $this->facebookService->client( $this->getUser()['token'] );
        $response = $client->get('/posts?ids=' . $pagesListWithCommas .'&limit=10&fields=from{name,picture,link},message,full_picture,created_time,link');

        foreach ($response->getDecodedBody() as $page) {
            if (!empty($page['data'])) {
                $list = array_merge($list, $page['data']);
            }
        }

        if (!empty($list)) {
            foreach ($list as $i => $post) {
                $list[ $i ]['created_at_timestamp'] = strtotime($post['created_time']);

                if (!empty($post['message'])) {
                    $list[ $i ]['message'] = $this->convertPlainTextLinks($post['message']);
                }
            }

            usort($list, function($a, $b) {
                return $b['created_at_timestamp'] - $a['created_at_timestamp'];
            });
        }

        return ['list' => $list];
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

        return [
            'categories' => $this->finalCategories($categorisedPages), 
            'all' => $this->finalAllPages($totalPages),
            'selected' => $this->getSelectedPages(true)
        ];
    }

    public function getSelectedPages($onlyIds = false)
    {
        $pages = UsersPages::where('user_id', $this->getUser()['id'])->get(['page_id']);

        if ($onlyIds) {
            return $pages->pluck('page_id')->toArray();
        }

        return $pages;
    }

    public function saveSettings(FeedSaveSettings $request)
    {
        try {
            UsersPages::where('user_id', $this->getUser()['id'])->delete();

            foreach ($request->input('pages') as $pageId) {
                UsersPages::create([
                    'user_id' => $this->getUser()['id'],
                    'page_id' => $pageId
                ]);
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }

        return ['message' => 'Selected pages have been saved.'];
    }

    private function convertPlainTextLinks($text)
    {
        $url = '@(http)?(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
        return preg_replace($url, '<a href="http$2://$4" target="_blank">$0</a>', $text);
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

    private function finalAllPages($pages = [])
    {
        $finalArray = [];
        $pages = array_unique($pages, SORT_REGULAR);
        foreach ($pages as $page) {
            $finalArray[] = $page;
        }

        return $finalArray;
    }
}