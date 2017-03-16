<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'social_id', 'name', 'email', 'password', 'token', 'last_visit_first_post_date'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];
    
    public function players()
    {
        return $this->hasMany('App\Models\UserWebPushPlayer');
    }
    
    public function pages()
    {
        return $this->hasMany('App\Models\UsersPages');
    }
    
    public function scopeWithAndWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)->with([$relation => $constraint]);
    }
}