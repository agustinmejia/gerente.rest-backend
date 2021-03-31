<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBranch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'product_id', 'stock'
    ];

    public function branch(){
        return $this->belongsTo(Branch::class,'branch_id');
    }
}
