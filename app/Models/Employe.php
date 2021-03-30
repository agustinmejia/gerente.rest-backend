<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    use HasFactory;

    protected $fillable = ['person_id', 'user_id', 'branch_id', 'status'];

    public function person(){
        return $this->belongsTo('\App\Models\Person', 'person_id');
    }

    public function user(){
        return $this->belongsTo('\App\Models\User', 'user_id');
    }

    public function branch(){
        return $this->belongsTo('\App\Models\Branch', 'branch_id');
    }
}
