<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'company_id', 'product_category_id', 'name', 'type', 'slug', 'short_description', 'long_description', 'price', 'image', 'views'
    ];

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
            ->generateSlugsFrom(['name', 'type'])
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(){
        return 'slug';
    }

    public function category(){
        return $this->belongsTo(ProductCategory::class,'product_category_id');
    }

    public function stock(){
        return $this->hasMany(ProductBranch::class);
    }
}
