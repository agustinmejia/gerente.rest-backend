<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillDosage extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'authorization_number',
        'dosing_cock',
        'limit_date',
        'initial_number',
        'current_number',
        'status'
    ];
}
