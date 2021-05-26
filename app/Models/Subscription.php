<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'subscriptions_type_id', 'payment_type', 'start', 'end', 'status'
    ];

    public function type(){
        return $this->belongsTo(SubscriptionsType::class, 'subscriptions_type_id');
    }
}
