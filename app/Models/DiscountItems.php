<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountItems extends Model
{
    use HasFactory;
    protected $table = 'discount_items';
    protected $fillable = ['discount_id','item_id'];
}
