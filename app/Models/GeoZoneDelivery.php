<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoZoneDelivery extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',  'radius','price','colorarea', 'restorant_id', 'active',
    ];
    protected $table = 'geo_zone_delivery';
    public function restorant()
    {
        return $this->belongsTo(\App\Restorant::class);
    }
}
