<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'price',
        'quantity',
        'additional_product'
    ];

    public function product(){
        return $this->belongsTo('\App\Models\Product', 'product_id');
    }

    public function sale(){
        return $this->belongsTo('\App\Models\Sale', 'sale_id');
    }
}
