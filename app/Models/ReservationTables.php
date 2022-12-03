<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationTables extends Model
{
    use HasFactory;
    protected $table = 'reservations_tables';
    protected $fillable = [
        'companie_id', 'table_id','price'
    ];
}
