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
        'sales_status_id',
        'total',
        'discount',
        'paid_out',
        'table_number',
        'amount_received',
        'observations'
    ];

    public function employe(){
        return $this->belongsTo('\App\Models\User', 'user_id');
    }

    public function customer(){
        return $this->belongsTo('\App\Models\Customer', 'customer_id');
    }

    public function branch(){
        return $this->belongsTo('\App\Models\Branch', 'branch_id');
    }

    public function details(){
        return $this->hasMany(SalesDetail::class);
    }

    public function status(){
        return $this->belongsTo('\App\Models\SalesStatus', 'sales_status_id');
    }

    public function cashier(){
        return $this->belongsTo('\App\Models\Cashier', 'cashier_id');
    }
}
