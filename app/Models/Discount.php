<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;
    protected $table = 'discount';
    protected $fillable = [ 'companie_id','name', 'opcion_discount', 'type', 'price', 'active_from', 'active_to', 'used_count','active','items_ids'];

    public function calculateDeduct($currentCartValue){
        if( Carbon::now()->between(new Carbon($this->active_from),new Carbon($this->active_to))){
            if ($this->type == 0) {
                if($this->price>$currentCartValue){
                    return 0;
                }
                return $this->price;
            }else{
                $des = round(($this->price / 100)*$currentCartValue,2);
                if($des>$currentCartValue){
                    return 0;
                }
                return $des;
            }
        }else{
            return 0;
        }
    }

}

