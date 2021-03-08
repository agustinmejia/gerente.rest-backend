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

    public function user(){
        return $this->belongsTo('\App\Models\User', 'user_id');
    }

    public function branch(){
        return $this->belongsTo('\App\Models\Branch', 'branch_id');
    }
}
