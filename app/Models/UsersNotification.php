<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'body', 'status'
    ];
}
