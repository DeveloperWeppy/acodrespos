<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncuestaOrden extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'deleted_at'];
    protected $table = 'encuesta_ordens';
}
