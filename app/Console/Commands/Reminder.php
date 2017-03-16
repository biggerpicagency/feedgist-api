<?php

namespace App\Console\Commands;

use App\Notifications\UnreadPosts;
use App\Services\ReminderService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Reminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminds the users about unread posts';
    
    protected $reminderService;
    
    /**
     * Create a new command instance.
     *
     * @param ReminderService $reminderService
     */
    public function __construct(ReminderService $reminderService)
    {
        parent::__construct();
        $this->reminderService = $reminderService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now();
        $minutes = ($date->minute < 10) ? '0' . $date->minute : $date->minute;
        $time = $date->hour . ':' . $minutes;
        
        $users = $this->reminderService->getUsers($time);
        foreach ($users as $user) {
            $response = (object) $this->reminderService->getCount($user);
    
            if (!empty($user->players) && $response->countPosts) {
                
                foreach ($user->players as $player) {
                    $player->notify(new UnreadPosts($response));
                }
            }
        }
        
    }
}
