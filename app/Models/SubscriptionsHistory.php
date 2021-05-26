<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionsHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id', 'user_id', 'amount', 'observations'
    ];
}
