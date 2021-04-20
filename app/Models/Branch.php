<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Branch extends Model
{
    use HasFactory;
    use HasSlug;
    
    public $table = 'branches';

    protected $fillable = [
        'company_id', 'name', 'slug', 'city_id', 'location', 'address', 'phones', 'status'
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

    public function sales(){
        return $this->hasMany('\App\Models\Sale', 'branch_id');
    }

    public function company(){
        return $this->belongsTo('\App\Models\Company', 'company_id');
    }
}
