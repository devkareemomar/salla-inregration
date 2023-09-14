<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Model;


class Workspace extends  Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'token',
        'merchant_id',
        'channelId',
        'is_ready',
        'is_active',
    ];

    public function merchant()
    {
         return $this->belongsTo(Merchant::class);
    }

    public function templates()
    {
         return $this->hasMany(Template::class);
    }
}
