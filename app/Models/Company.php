<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Company extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'owner_id', 'name', 'slug', 'slogan', 'small_description', 'long_description', 'city_id', 'address', 'phones', 'logos', 'banners', 'nit', 'activity_description', 'email'
    ];

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(){
        return 'slug';
    }

    public function city(){
        return $this->belongsTo('\App\Models\City', 'city_id');
    }
}
