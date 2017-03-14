<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWebPushPlayer extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_web_push_players';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'player_id', 'reminder_type', 'reminder_first_at', 'reminder_second_at'];
}