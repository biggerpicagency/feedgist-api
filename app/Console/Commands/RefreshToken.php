<?php

namespace App\Console\Commands;

use App\Services\FacebookService;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh access token for users created more than 2 days ago';
    
    protected $facebookService;
    
    /**
     * Create a new command instance.
     * @param FacebookService $facebookService
     */
    public function __construct(FacebookService $facebookService)
    {
        parent::__construct();
        $this->facebookService = $facebookService;
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $users = User::having(DB::raw('MOD(DATEDIFF(`created_at`, "' . Carbon::now() . '"), 7)'), 0)->get();

        foreach ($users as $user) {
            $accessToken = $this->facebookService->getRefreshedToken($user->token);
            $user->token = $accessToken;
            $user->update();
        }
    }
}
