<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_number',
        'control_code',
        'bill_dosage_id',
        'status',
        'amount',
        'amount_ice',
        'amount_exempt',
        'zero_rate',
        'subtotal',
        'discount',
        'base_amount',
        'fiscal_debit'
    ];
}
