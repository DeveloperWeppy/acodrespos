<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;
    protected $table = 'reservations';
    protected $fillable = [
        'companie_id', 'client_id','check_percentage', 'reservation_reason_id', 'description', 'payment_method','payment_status','active','note','observations','date','total'
    ];
}
