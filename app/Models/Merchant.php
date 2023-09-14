<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;


class Merchant extends  Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'merchant',
        'access_token',
        'refresh_token',
        'email',
        'phone',
        'status',
    ];
    public function events()
    {
         return $this->hasMany(ComingEvent::class,'merchant_id','merchant');
    }
}
