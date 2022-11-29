<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationReason extends Model
{
    use HasFactory;

    protected $fillable = [
        'companie_id', 'name', 'description', 'price', 'active'
    ];
}
