<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'event',
        'order_id',
        'status',
        'customer_name',
        'customer_phone',
        'message',
        'event_json',
    ];
}
