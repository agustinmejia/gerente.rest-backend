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
        'owner_id', 'companies_type_id', 'name', 'slug', 'slogan', 'short_description', 'long_description', 'city_id', 'address', 'phones', 'logos', 'banners', 'nit', 'activity_description', 'email'
    ];

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(){
        return 'slug';
    }

    public function type(){
        return $this->belongsTo(CompaniesType::class, 'companies_type_id');
    }

    public function city(){
        return $this->belongsTo(City::class, 'city_id');
    }

    public function branches(){
        return $this->hasMany(Branch::class);
    }
}
