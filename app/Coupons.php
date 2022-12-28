<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupons extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'name', 'code', 'restaurant_id', 'type', 'price', 'active_from', 'active_to', 'limit_to_num_uses','redemption','min_price_cart','has_ilimited','has_free_delivery','has_discount','active',
    ];

    public function calculateDeduct($currentCartValue){
        if( (Carbon::now()->between(new Carbon($this->active_from),new Carbon($this->active_to)) && $this->limit_to_num_uses >0 && $currentCartValue>=$this->min_price_cart) || ($this->has_ilimited==1 && $this->limit_to_num_uses >0 && $currentCartValue>=$this->min_price_cart)){
          
            if($this->has_discount==1 || $this->has_free_delivery==1){
                return 1;
            }
            
            if ($this->type == 0) {
                return $this->price;
            }else{
                return round(($this->price / 100)*$currentCartValue,2);
            }
        }else{
            return null;
        }
        
    }
}
