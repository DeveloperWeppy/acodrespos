<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationConfig extends Model
{
    use HasFactory;
    protected $table = 'reservations_config';
    protected $fillable = [
        'companie_id', 'minimum_period','condition_period', 'percentage_payment', 'wait_time', 'standard_price','check_no_cost'
    ];
}
