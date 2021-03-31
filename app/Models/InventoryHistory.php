<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id', 'branch_origen_id', 'user_id', 'product_id', 'stock'
    ];
}
