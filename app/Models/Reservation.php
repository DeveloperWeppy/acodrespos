<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Reservation extends Model
{
    use HasFactory;
    use Notifiable;
    
    protected $table = 'reservations';
    protected $fillable = [
        'companie_id', 'client_id','check_percentage', 'reservation_reason_id', 'description', 'mesas', 'personas', 'payment_status', 'reservation_status', 'active','note','observations','date_reservation','total','update_price','pendiente','payment_1','payment_2','payment_3','url_payment1','url_payment2','url_payment3'
    ];
}
