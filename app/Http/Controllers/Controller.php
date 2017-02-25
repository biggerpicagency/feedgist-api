<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\Services\FacebookService;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $user;
    private $request;
    protected $facebook;

    public function __construct(Request $request, FacebookService $facebookService)
    {
        $this->request = $request;
        $this->facebook = $facebookService;

        if (!empty($this->request->input('user'))) {
            $this->user = $this->request->input('user');
        }
    }

    public function apiResponse($data = null)
    {
        $status = !empty($data['error']) ? 400 : 200;

        if (empty($data['token']) && !empty($this->request->input('token'))) {
            $data['token'] = $this->request->input('token');
        }

        return response()->json($data, $status);
    }

    public function facebookClient()
    {
        return $this->facebook->client( $this->request->input('user')['token'] );
    }

    public function getUser()
    {
        return $this->request->input('user');
    }
}
