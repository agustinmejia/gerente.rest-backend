<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionsType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'expiration_days', 'status', 'color', 'icon'
    ];
}
