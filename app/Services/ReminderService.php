<?php

namespace App\Services;

use App\User;

class ReminderService
{
    protected $facebookService;
    public function __construct(FacebookService $facebookService)
    {
        $this->facebookService = $facebookService;
    }
    
    public function getUsers($time)
    {
        $users = User::withAndWhereHas('players', function ($query) use ($time) {
            $query->where('reminder_first_at', $time)->orWhere('reminder_second_at', $time);
        })->get();
        return $users;
    }
    
    public function getCount($user)
    {
        $usersLastVisit = strtotime($user->last_visit_first_post_date);
        $pages = $user->pages()->pluck('page_id')->toArray();
        $pagesListWithCommas = implode(',', $pages);
    
        if (empty($pagesListWithCommas)) {
            return [
                'countPosts' => 0
            ];
        }
        
        $client = $this->facebookService->client($user->token);
        $response = $client->get('/posts?ids=' . $pagesListWithCommas .'&limit=5&fields=from{name,picture,link},created_time');
        $pages = $response->getDecodedBody();
    
        $pagesNames = [];
        $countPosts = 0;
        
        foreach ($pages as $index => $page) {
            $pageAdded = false;
            
            if (!empty($page['data'])) {
                $data = $page['data'];
                foreach ($data as $post) {
                    $postsDate = strtotime($post['created_time']);
                    
                    if ($postsDate >= $usersLastVisit) {
                        $countPosts++;
                        
                        if (!$pageAdded) {
                            array_push($pagesNames, $post['from']['name']);
                            $pageAdded = true;
                        }
                    }
                    
                    
                }
            }
        }
        $limit = 2;
        return [
            'countPosts' => $countPosts,
            'pagesNames' => implode(', ', array_slice($pagesNames, 0, $limit)),
            'pagesCount' => count($pagesNames) - $limit
        ];
    }
}