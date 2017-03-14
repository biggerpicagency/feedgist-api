<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SettingsService;
use App\Http\Requests\SettingsSave;
use App\Http\Requests\SettingsRemove;

class SettingsController extends Controller
{
    public function get($playerId, Request $request, SettingsService $settingsService)
    {
        return $this->apiResponse( $settingsService->getSettings($playerId, $request->get('user')['id']) );
    }

    public function add(SettingsSave $request, SettingsService $settingsService)
    {
        return $this->apiResponse( $settingsService->save($request, $request->get('user')['id']) );
    }

    public function remove($playerId, Request $request, SettingsService $settingsService)
    {
        return $this->apiResponse( $settingsService->remove($playerId, $request->get('user')['id']) );
    }
}
