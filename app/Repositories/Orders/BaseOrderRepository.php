<?php

namespace App\Repositories\Orders;

use App\Items;
use App\Order;
use App\Coupons;
use App\Models\Log;
use App\Models\Variants;
use App\Restorant as Vendor;
use Illuminate\Http\Request;
use App\Models\RestaurantClient;
use App\Events\OrderAcceptedByAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OrderNotification;
use Illuminate\Support\Facades\Validator;
use App\Events\NewOrder as PusherNewOrder;

use App\Models\GeoZoneDelivery;
use App\Address;


class BaseOrderRepository extends Controller
{

    /**
     * @var Request request - The request made
     */
    public $request;

    /**
     * @var Vendor vendor - The vendor
     */
    public $vendor;

    /**
     * @var Order order - The order
     */
    public $order;

    /**
     * @var string expedition - Deliver - 1, PickUp -2, Dine in -3
     */
    public $expedition;

    /**
     * @var bool hasPayment
     */
    public $hasPayment;

    /**
     * @var bool isStripe
     */
    public $isStripe;

    /**
     * @var bool status
     */
    public $status=true;

    /**
     * @var bool isNewOrder
     */
    public $isNewOrder=true;

    /**
     * @var string errorMessage - Deliver, DineIn, PickUp
     */
    public $errorMessage="";

    /**
     * @var Redirect paymentRedirect
     */
    public $paymentRedirect=null;

     /**
     * @var bool isMobileOrder
     */
    public $isMobileOrder=false;


    /**
     * @var string redirectLink
     */
    public $redirectLink;

    public function __construct($vendor_id,$request,$expedition,$hasPayment,$isStripe){
        $this->request=$request;
        $this->expedition=$expedition;
        $this->hasPayment=$hasPayment;
        $this->isStripe=$isStripe;

        //Set the Vendor
        if($vendor_id){
            $this->vendor = Vendor::findOrFail($vendor_id);
        }else{
            $this->vendor=null;
        }
       
    }

    

    public function constructOrder(){
        //Create the order 
        $this->createOrder();

        //Set Items
        $this->setItems();

        //Set Comment
        $this->setComment();

        //Calculate fees
        $this->calculateFees();

    }

    public function validateOrder(){
        $validator = Validator::make(['order_price'=>$this->order->order_price], [
            'order_price'=>['numeric','min:0'.$this->vendor->minimum]
        ]);
        if($validator->fails()){
            $this->invalidateOrder();
        }
        return $validator;
    }

    public function invalidateOrder(){
        $this->status=false;
        $this->order->delete();
    }

    public function updateOrder(){
        //Store it if not stored yet, otherwise update it
        $this->order->update();
    }

    public function finalizeOrder(){
    }

    private function createOrder(){
        $function = $this->getIpLocation();
        
        if($this->order==null){
            $this->order=new Order;
            $this->order->restorant_id=$this->vendor?$this->vendor->id:null;
            $this->order->comment="";
            $this->order->payment_method=$this->request->payment_method;
            $this->order->payment_status="unpaid";

            $expeditionsTypes=['delivery'=>1,'pickup'=>2,'dinein'=>3]; //1- delivery 2 - pickup 3-dinein
            $this->order->delivery_method=$expeditionsTypes[$this->expedition];  

            //Client
            if(auth()->user()){
                $this->order->client_id=auth()->user()->id;
                $client=RestaurantClient::where(['companie_id'=>$this->order->restorant_id,'user_id'=>$this->order->client_id])->count();
                if($client==0){
                    $resUser = RestaurantClient::create([
                        'user_id' => $this->order->client_id,
                        'companie_id' =>$this->order->restorant_id,
                    ]);
                }
            }

             

            $this->order->order_price=0;
            $this->order->vatvalue=0;

            //Save order
            $this->order->save();
            
            $this->order->md=md5($this->order->id);
            if (isset($this->request->id_account_bank)) {
                $this->order->id_account_bank =$this->request->id_account_bank;
            }
                
            if (isset($this->request->img_evidencia)) {
                $nom=$this->order->id.".png";
                $path = 'uploads/payments/';
                $this->order->url_payment =$path.$nom;
                $this->request->img_evidencia->move(public_path($path), $nom);
            }
            $this->order->update();

 

            //Save order custom fields
            $this->order->setMultipleConfig($this->request->customFields);


        }else{
            //Order is already initialized - in case of continues ordering
            $this->isNewOrder=false;
        }
    }
    
    private function setItems(){

        foreach ($this->request->items as $key => $item) {

            
            //Obtain the item
            $theItem = Items::findOrFail($item['id']);

            //List of extras
            $extras = [];

            $dsc = Vendor::applyDiscount($theItem->discount_id,$theItem->price);
            
            //The price of the item or variant
            $itemSelectedPrice = $theItem->price-$dsc;

            //Find the variant
            $variantName = '';
            if ($item['variant']) {
                //Find the variant
                $variant = Variants::findOrFail($item['variant']);
                $itemSelectedPrice = $variant->price-$dsc;
                $variantName = $variant->optionsList;
            }

           //Find the extras
            foreach ($item['extrasSelected'] as $key => $extra) {
                $theExtra = $theItem->extras()->findOrFail($extra['id']);
                $itemSelectedPrice+=$theExtra->price;
                array_push($extras, $theExtra->name.' + '.money($theExtra->price, config('settings.cashier_currency'), config('settings.do_convertion')));
            }

            //Descuento vigente del producto
           
            
            
          

            
            //Total vat on this item
            $totalCalculatedVAT = $item['qty'] * ($theItem->vat > 0?($itemSelectedPrice) * ($theItem->vat / 100):0);
            $cart_item_id=0;
            $item_observacion="";
            if(isset($item['cart_item_id'])){
                $cart_item_id=$item['cart_item_id'];
            }
            if(isset($item['item_observacion'])){
                $item_observacion=$item['item_observacion'];
            }
            $this->order->items()->attach($item['id'], [
                'qty'=>$item['qty'], 
                'extras'=>json_encode($extras), 
                'vat'=>$theItem->vat, 
                'vatvalue'=>$totalCalculatedVAT,
                'variant_name'=>$variantName, 
                'variant_price'=>$itemSelectedPrice,
                'item_status'=>'cocina',
                'item_observacion'=>$item_observacion,
                'cart_item_id'=>$cart_item_id,
                'discount'=>$dsc
            ]);
        } 


        //After we have updated the list of items, we need to update the order price
        $order_price=0;
        $total_order_vat=0;
        foreach ($this->order->items()->get() as $key => $item) {
            $order_price+=$item->pivot->qty*($item->pivot->variant_price-$item->pivot->discount);
            $total_order_vat+=$item->pivot->vatvalue;
        }
        $this->order->order_price=$order_price;
        $this->order->vatvalue=$total_order_vat;

        
        //Set coupons
        if($this->request->has('coupon_code')&&strlen($this->request->coupon_code)>0){
            $coupon = Coupons::where(['code' => $this->request->coupon_code])->where('restaurant_id',$this->vendor->id)->get()->first();
            if($coupon){

                $deduct = 0;

                if($coupon->has_free_delivery==1){
                    $addresss = Address::findOrFail($this->request->address_id);
                    $restaurantzona=GeoZoneDelivery::where('restorant_id',$this->vendor->id)->get();
                    $addressesWithFees =$this->getAccessibleAddresses2($restaurantzona, [$addresss]);
                    $cost_total=0;
                    foreach ($addressesWithFees as $key => $addressWithFee) {
                        $cost_total=$addressWithFee->cost_total;
                    }
                    $deduct = $cost_total;
                }

                if($coupon->has_free_delivery==0){
                    $deduct=$coupon->calculateDeduct($this->order->order_price);
                }

                if($order_price<$coupon->min_price_cart){
                    $deduct = 0;
                }

                if($coupon->redemption!=0){
                    $redemption = Order::where(['coupon'=> $this->request->coupon_code, 'client_id'=>auth()->user()->id])->count();
                    if($redemption>=$coupon->redemption){
                        $deduct = 0;
                    }
                }
                
                if($deduct!=0){
                    $coupon->decrement('limit_to_num_uses');
                    $coupon->increment('used_count');
                    $this->order->coupon=$this->request->coupon_code;
                    if($deduct>$this->order->order_price){
                        $this->order->discount=$order_price;

                        //In this case, order should be considered as paid one
                        //$this->order->payment_status = 'paid';
                    }else{
                        $this->order->discount=$deduct;
                    }
                }
            }
        }

        

        

        //Update the order with the item
        $this->order->update();
    }

    private function setComment(){
       
        $comment = $this->request->comment ? strip_tags($this->request->comment.'') : '';
        $this->order->comment = $this->order->comment.' '.$comment;
        $this->order->update();
    }

    private function calculateFees(){
        if($this->vendor){
            $this->order->static_fee=$this->vendor->static_fee;
            $this->order->fee=$this->vendor->fee;
            $this->order->fee_value=($this->vendor->fee/100)*($this->order->order_price_with_discount-$this->vendor->static_fee);
            $this->order->update();
        }
        
    }

    public function notifyAdmin(){
        //Does nothing
    }

    public function notifyOwner(){
        //Inform owner - via email, sms or db
        $this->vendor->user->notify((new OrderNotification($this->order,1,$this->vendor->user))->locale(strtolower(config('settings.app_locale'))));

        //Notify owner with pusher
        if (strlen(config('broadcasting.connections.pusher.secret')) > 4) {
            event(new PusherNewOrder($this->order, __('notifications_notification_neworder')));
        }

        //Dispatch Approved by admin event
        OrderAcceptedByAdmin::dispatch($this->order);
    }
}