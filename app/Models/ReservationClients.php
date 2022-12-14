<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationClients extends Model
{
    use HasFactory;
    protected $table = 'reservations_clients';
    protected $fillable = [
        'reservation_id', 'client_id','table_id','date_reservation',
    ];
}

