<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id',
        'user_id'
    ];

    public function person(){
        return $this->belongsTo('\App\Models\Person', 'person_id');
    }
}
