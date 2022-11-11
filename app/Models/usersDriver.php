<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class usersDriver extends Model
{
    use HasFactory;
    protected $table = 'users_driver';
    protected $fillable = ['order_id', 'name', 'phone'];
}
