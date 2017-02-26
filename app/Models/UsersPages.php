<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersPages extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users_pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'page_id'];

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }
}