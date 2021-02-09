<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'customer_id',
        'user_id',
        'cashier_id',
        'sale_number',
        'payment_type',
        'sale_type',
        'total',
        'discount',
        'paid_out',
        'table_number',
        'amount_received',
        'observations'
    ];

    public function customer(){
        return $this->belongsTo('\App\Models\Customer', 'customer_id');
    }
}
