<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'suscriptions_type_id', 'payment_type', 'start', 'end', 'status'
    ];
}
