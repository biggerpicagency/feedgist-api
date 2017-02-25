<?php

namespace App\Services;

use Illuminate\Http\Request;

class BaseService
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getUser()
    {
        return $this->request->input('user');
    }
}