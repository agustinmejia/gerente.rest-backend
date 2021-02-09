<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id',
        'user_id',
        'company_id'
    ];

    public function person(){
        return $this->belongsTo('\App\Models\Person', 'person_id');
    }

    public function company(){
        return $this->belongsTo('\App\Models\Company', 'company_id');
    }
}
