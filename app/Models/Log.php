<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip',
        'module',
        'submodule',
        'action',
        'detail',
        'country',
        'city',
        'lat',
        'lon',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
