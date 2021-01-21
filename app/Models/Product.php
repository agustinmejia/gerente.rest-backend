<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'name', 'slug', 'type', 'short_description', 'long_description', 'price', 'image', 'views'
    ];
}
