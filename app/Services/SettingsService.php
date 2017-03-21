<?php

namespace App\Services;

use App\Mail\EmailToAdmin;
use Illuminate\Http\Request;
use App\Services\BaseService;
use App\Models\UserWebPushPlayer;
use Illuminate\Support\Facades\Mail;

class SettingsService extends BaseService
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function getSettings($playerId, $userId)
    {
        $settings = UserWebPushPlayer::where('player_id', $playerId)->where('user_id', $userId)->first();

        if (!$settings) {
            return ['error' => 'Alerts Settings not found.'];
        }

        return $settings;
    }

    public function save($request, $userId)
    {
        $adding = false;
        $data = $request->all();
        $settings = UserWebPushPlayer::where('player_id', $data['player_id'])->where('user_id', $userId)->first();

        if (!$settings) {
            $settings = new UserWebPushPlayer;
            $adding = true;
        }

        try {
            $settings->user_id = $userId;
            $settings->player_id = $data['player_id'];
            $settings->reminder_type = $data['reminder_type'];
            $settings->reminder_first_at = $data['reminder_first_at'];
            $settings->reminder_second_at = $data['reminder_second_at'];
            $settings->save();

            return ['message' => 'Alerts Settings for this device have been saved.'];
        } catch (Exception $e) {
            return ['error' => 'Error while saving alerts settings occured.'];
        }
    }

    public function remove($playerId, $userId)
    {
        try {
            UserWebPushPlayer::where('player_id', $playerId)->where('user_id', $userId)->delete();
            return ['message' => 'Your device has been removed from Alerts.'];
        } catch (Exception $e) {
            return ['error' => 'Error while removing your device from Alerts.'];
        }
    }
    
    public function sendMessage($request)
    {
        $user = $request->get('user');
        $emailData = [
            'message' => $request->get('message', '*Something went wrong with getting the input*'),
            'usersName' => $user['name'],
            'usersEmail' => $user['email']
        ];
        Mail::to(env('CONTACT_FORM_EMAIL'))->send(new EmailToAdmin($emailData));
        return ['message' => 'Thank you for your message.'];
    }
    
}