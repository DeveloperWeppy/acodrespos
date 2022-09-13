<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaKitchen extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'colorarea', 'restorant_id', 'active',
    ];

    public function restorant()
    {
        return $this->belongsTo(\App\Restorant::class);
    }

    public function categories()
    {
        return $this->belongsTo(\App\Categories::class);
    }
}
