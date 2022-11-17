<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigCuentasBancarias extends Model
{
    use HasFactory;

    protected $fillable = ['rid', 'name_receptor', 'name_bank', 'type_document', 'number_document', 'type_account', 'number_account'];
}
