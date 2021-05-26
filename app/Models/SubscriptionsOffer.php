<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionsOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscriptions_type_id', 'discount', 'start', 'end', 'status'
    ];
}
