<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncuestaClient extends Model
{
    use HasFactory;

    protected $fillable = ['id_question', 'answer', 'id_ratings'];

    protected $table = 'encuesta_clients';

    public function questions()
    {
        return $this->hasMany(\App\Models\EncuestaOrden::class);
    }

    public function encuestaclient()
    {
        return $this->belongsTo(\App\Models\EncuestaClient::class);
    }
}
