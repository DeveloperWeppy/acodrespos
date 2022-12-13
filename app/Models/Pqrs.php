<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pqrs extends Model
{
    use Notifiable;
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'type_radicate',
        'num_order',
        'order_id',
        'message',
        'evidence',
        'status',
        'answer_radicate',
        'evidence_answer'
    ];
    
}
