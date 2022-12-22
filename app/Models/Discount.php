<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $table = 'discount';
    protected $fillable = ['companie_id','name', 'opcion_discount', 'type', 'price', 'active_from', 'active_to', 'used_count'];
}

