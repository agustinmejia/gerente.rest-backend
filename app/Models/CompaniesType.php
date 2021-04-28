<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class CompaniesType extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'name',
        'plural_name',
        'slug',
        'icon',
        'image',
        'color',
        'status'
    ];

    public function getSlugOptions() : SlugOptions{
        return SlugOptions::create()
            ->generateSlugsFrom('plural_name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(){
        return 'slug';
    }

    public function companies(){
        return $this->hasMany(Company::class);
    }
}
