<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashier extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'name',
        'observations',
        'opening',
        'closing',
        'opening_amount',
        'closing_amount',
        'real_amount',
        'missing_amount',
        'status'
    ];
}
