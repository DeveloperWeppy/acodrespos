<?php

namespace App\Http\Controllers;

use Cart;
use App\Items;
use App\Order;
use App\Plans;
use App\Tables;
use App\Restorant;
use Carbon\Carbon;
use App\Traits\Fields;
use App\Models\Variants;
use App\Models\Orderitems;
use Illuminate\Http\Request;
use App\Services\ConfChanger;
use App\Models\CartStorageModel;
use App\Models\GeoZoneDelivery;
use Illuminate\Support\Facades\Auth;
use Akaunting\Module\Facade as Module;
use App\Models\ConfigCuentasBancarias;
use Illuminate\Support\Facades\Cookie;

class CartController extends Controller
{
    use Fields;

    private function setSessionID($session_id){
        //We have session ID only from POS. So then use the CartDBStorageRepository
        config(['shopping_cart.storage' => \App\Repositories\CartDBStorageRepository::class]); 
        Cart::session($session_id);
    }

    public function add(Request $request)
    {
        if(isset($request->session_id)){
            $this->setSessionID($request->session_id);
        }
    
        $item = Items::find($request->id);
        $restID = $item->category->restorant->id;

        $restaurant = Restorant::findOrFail($restID);
        \App\Services\ConfChanger::switchCurrency($restaurant);
        

        //Check if added item is from the same restorant as previus items in cart
        $canAdd = false;
        if (Cart::getContent()->isEmpty()) {
            $canAdd = true;
        } else {
            $canAdd = true;
            foreach (Cart::getContent() as $key => $cartItem) {
                if ($cartItem->attributes->restorant_id.'' != $restID.'') {
                    $canAdd = false;
                    break;
                }
            }
        }

        if ($item && $canAdd) {

            //are there any extras
            $cartItemPrice = $item->price;
            $cartItemName = $item->name;
            $theElement = '';


            //Descuento vigente del producto
            $discount = $restaurant->applyDiscount($item->discount_id,$item->price);
            if($discount>0){
                $cartItemPrice = $item->price-$discount;
            }


            //variantID
            if ($request->variantID) {
                //Get the variant
                $variant = Variants::findOrFail($request->variantID);

                //Validate is this variant is from the current item
                if ($variant->item->id == $item->id) {
                    $cartItemPrice = $variant->price;

                    //For each option, find the option on the
                    $cartItemName = $item->name.' '.$variant->optionsList;
                }
            }

            foreach ($request->extras as $key => $value) {
                $cartItemName .= "\n+ ".$item->extras()->findOrFail($value)->name;
                $cartItemPrice += $item->extras()->findOrFail($value)->price;
                $theElement .= $value.' -- '.$item->extras()->findOrFail($value)->name.'  --> '.$cartItemPrice.' ->- ';
            }

            Cart::add((new \DateTime())->getTimestamp(), $cartItemName, $cartItemPrice, $request->quantity, $request->personaccount, ['id'=>$item->id, 'variant'=>$request->variantID, 'extras'=>$request->extras, 'restorant_id'=>$restID, 'image'=>$item->icon, 'friendly_price'=>  Money($cartItemPrice, config('settings.cashier_currency'), config('settings.do_convertion'))->format()]);

            return response()->json([
                'status' => true,
                'errMsg' => $theElement,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errMsg' => __("You can't add items from other restaurant!"),
            ]);
        }
    }

    public function getContent()
    {

        //In this case, we need to use the cookies for storagee
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }

        return response()->json([
            'data' => Cart::getContent(),
            'total' => Cart::getSubTotal(),
            'status' => true,
            'errMsg' => '',
        ]);
    }

    public function getContentPOS()
    {
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }else{
            return response()->json([
                'status' => false,
                'errMsg' => 'Session is not started yet',
            ]);
        }
        

        $cs=CartStorageModel::where('id',$_GET['session_id']."_cart_items")->first();
        $orderCart=Order::where(['cart_storage_id'=>$_GET['session_id']."_cart_items","payment_status"=>"unpaid"])->orderBy('id', 'DESC')->first();
        $arrayItem=array();
        $order_id=0;
        $comment="";
        $arrayAddId=array();
        if(isset($orderCart->id)){
            $order_id=$orderCart->id;
            $comment=$orderCart->comment;
            $order = Order::findOrFail($orderCart->id);
            $orderStatus= $order->stakeholders("DESC")->get();
            $order=$order->items()->get();
            if(count($orderStatus)>0){
                if($orderStatus[0]->pivot->status_id==9){
                    $order_id=0;
                }
            }
            if(count($order)>0 && $order_id>0){
                $cartObj=json_decode(json_encode(Cart::getContent()),true);
                $cartKey=array_keys($cartObj);
                foreach ($cartKey as $key => $item) {
                    $tItems=$cartObj[$item];
                    $tItems['order_has_items_id']=0;
                    $tItems['qty']=1;
                    $tItems['status_id']=0;
                    foreach ($order as $key2 => $item2) {
                        if($tItems['attributes']['id']==$item2->id && $tItems['id']==$item2->pivot->cart_item_id){
                            $tItems['order_has_items_id']=$item2->pivot->id;
                            $tItems['qty']=$item2->pivot->qty;
                            $tItems['status_id']=$orderStatus[0]->pivot->status_id;
                            array_push($arrayAddId,$item2->pivot->id);
                        }
                    }
                    $arrayItem[$item]=$tItems;
                }
            }
        }else{
            $arrayItem=Cart::getContent();
        }
        return response()->json([
            'data' => $arrayItem,
            'config'=> $cs?$cs->getAllConfigs():[],
            'id'=>$_GET['session_id'],
            'order_id'=>$order_id,
            'comment'=>$comment,
            'total' => Cart::getSubTotal(),
            'status' => true,
            'errMsg' => '',
        ]);

    }

    public function cart()
    {
        
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }


        $fieldsToRender=[];
        if(strlen(config('global.order_fields'))>10){
            $fieldsToRender=$this->convertJSONToFields(json_decode(config('global.order_fields'),true)); 
        }
        $isEmpty = false;
        if (Cart::getContent()->isEmpty()) {
            $isEmpty = true;
        }

        if(!$isEmpty){
            //Cart is not empty
            $restorantID = null;
            foreach (Cart::getContent() as $key => $cartItem) {
                $restorantID = $cartItem->attributes->restorant_id;
                break;
            }

            
            $configaccountsbanks = ConfigCuentasBancarias::where('rid',$restorantID)->get();
            //The restaurant
            $restaurant = Restorant::findOrFail($restorantID);

            //Set config based on restaurant
            config(['app.timezone' => $restaurant->getConfig('time_zone',config('app.timezone'))]);

            

            $enablePayments=true;
            if(config('app.isqrsaas')){
                

                //In case, we use vendor defined Stripe, we need to check if keys are present
                if(config('settings.stripe_useVendor')){
                    if($restaurant->getConfig('stripe_enable')=="true"){
                        //We have stripe
                        config(['settings.enable_stripe' => true]);
                        config(['settings.stripe_key' => $restaurant->getConfig('stripe_key')]);
                        config(['settings.stripe_secret' => $restaurant->getConfig('stripe_secret')]);
                        config(['cashier.key' => $restaurant->getConfig('stripe_key')]);
                        config(['cashier.secret' => $restaurant->getConfig('stripe_secret')]);

                    }else{
                        //Stripe for this vendor is disabled
                        config(['settings.enable_stripe' => false]);
                    }
                }
            }

            //Change currency
            \App\Services\ConfChanger::switchCurrency($restaurant);

            //Create all the time slots
            $timeSlots = $this->getTimieSlots($restaurant);
           
            //user addresses
            $addresses = [];
            $ifAreaDelivery=false;
            if (config('app.isft')) {
                //$addresses = $this->getAccessibleAddresses($restaurant, auth()->user()->addresses->reverse());
                $restaurantzona=GeoZoneDelivery::where( [['restorant_id', '=',$restaurant->id],  ['active', '=',1]])->get();
               
                if(count($restaurantzona)>0){
                    $addresses =$this->getAccessibleAddresses2($restaurantzona,auth()->user()->addresses->reverse());
                    $ifAreaDelivery=true;
                }
                
            }

            $tables = Tables::where('restaurant_id', $restaurant->id)->get();
            $tablesData = [];
            foreach ($tables as $key => $table) {
                $tablesData[$table->id] = $table->full_name;
            }


            $extraPayments=[];
            foreach (Module::all() as $key => $module) {
                if($module->get('isPaymentModule')){
                    array_push($extraPayments,$module->get('alias'));
                }
            }
  
            $businessHours=$restaurant->getBusinessHours();
            $now = new \DateTime('now');

            //$formatter = new \IntlDateFormatter(config('app.locale'), \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
            $formatter = new \IntlDateFormatter('en', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
            $formatter->setPattern(config('settings.datetime_workinghours_display_format_new'));
        
            //Table ID
            $tid = Cookie::get('tid') ? Cookie::get('tid') :null;
            if($tid==""){$tid=null;}
            $tables=['ftype'=>'select', 'name'=>'', 'id'=>'table_id', 'placeholder'=>'Select table', 'data'=>$tablesData, 'required'=>true];
            $tableName="";
            if($tid!=null){
                $tables['value']=$tid;
                $tableName=Tables::findOrFail($tid)->full_name;
            }

            $doWeHaveOrderAfterHours=Module::has('orderdatetime')&&$restaurant->getConfig('order_date_time_enable',false);

            if($doWeHaveOrderAfterHours&&count($timeSlots)==0){
                $timeSlots=[null];
            }
            $params = [
                'configaccountsbanks'=>$configaccountsbanks,
                'type'=>'seleccione',
                'enablePayments'=>$enablePayments,
                'title' => 'Shopping Cart Checkout',
                'tables' =>  $tables,
                'restorant' => $restaurant,
                'timeSlots' => $timeSlots,
                'doWeHaveOrderAfterHours'=>$doWeHaveOrderAfterHours,
                'openingTime' => $businessHours->isClosed()?$formatter->format($businessHours->nextOpen($now)):null,
                'closingTime' => $businessHours->isOpen()?$formatter->format($businessHours->nextClose($now)):null,
                'addresses' => $addresses,
                'ifAreaDelivery'=>$ifAreaDelivery,
                'fieldsToRender'=>$fieldsToRender,
                'extraPayments'=>$extraPayments,
                'tid'=>$tid,
                'tableName'=>$tableName
            ];

            return view('cart')->with($params);
        }else{
            //Cart is empty
            if(config('app.isft')) {
                return redirect()->route('front')->withError('Your cart is empty!');
            }else{
                $previousOrders = Cookie::get('orders') ? Cookie::get('orders') : '';
                $previousOrderArray = array_filter(explode(',', $previousOrders));

                if(count($previousOrderArray) > 0){
                    foreach($previousOrderArray as $orderId){
                        $restorant = Order::where(['id'=>$orderId])->get()->first()->restorant;
                       
                        $restorantInfo = $this->getRestaurantInfo($restorant, $previousOrderArray);

                        return view('restorants.show', [
                            'restorant' => $restorantInfo['restorant'],
                            'openingTime' => $restorantInfo['openingTime'],
                            'closingTime' => $restorantInfo['closingTime'],
                            'usernames' => $restorantInfo['usernames'],
                            'canDoOrdering'=>$restorantInfo['canDoOrdering'],
                            'currentLanguage'=>$restorantInfo['currentLanguage'],
                            'showLanguagesSelector'=>$restorantInfo['showLanguagesSelector'],
                            'hasGuestOrders'=>$restorantInfo['hasGuestOrders'],
                            'fields'=>$restorantInfo['fields'],
                        ])->withError(__('Your cart is empty!'));
                    }
                }else{
                    return redirect()->route('front')->withError('Your cart is empty!');
                }                
            }
        }
    }

    public function getRestaurantInfo($restorant, $previousOrderArray)
    {
        //In QRsaas with plans, we need to check if they can add new items.
        $currentPlan = Plans::findOrFail($restorant->user->mplanid());
        $canDoOrdering = $currentPlan->enable_ordering == 1;

        //ratings usernames
        $usernames = [];
        if ($restorant && $restorant->ratings) {
            foreach ($restorant->ratings as $rating) {
                $user = User::where('id', $rating->user_id)->get()->first();

                if (! array_key_exists($user->id, $usernames)) {
                    $new_obj = (object) [];
                    $new_obj->name = $user->name;

                    $usernames[$user->id] = (object) $new_obj;
                }
            }
        }

        //Working hours
        $ourDateOfWeek = date('N') - 1;

        $format = 'G:i';
        if (config('settings.time_format') == 'AM/PM') {
            $format = 'g:i A';
        }

        //tables
        $tables = Tables::where('restaurant_id', $restorant->id)->get();
        $tablesData = [];
        foreach ($tables as $key => $table) {
            $tablesData[$table->id] = $table->restoarea ? $table->restoarea->name.' - '.$table->name : $table->name;
        }

        //Change Language
        ConfChanger::switchLanguage($restorant);

        //Change currency
        ConfChanger::switchCurrency($restorant);

        $currentEnvLanguage = isset(config('config.env')[2]['fields'][0]['data'][config('app.locale')]) ? config('config.env')[2]['fields'][0]['data'][config('app.locale')] : 'UNKNOWN';

        $businessHours=$restorant->getBusinessHours();
        $now = new \DateTime('now');

        $formatter = new \IntlDateFormatter(config('app.locale'), \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
        $formatter->setPattern(config('settings.datetime_workinghours_display_format_new'));

        return  [
            'restorant' => $restorant,
            'openingTime' => $businessHours->isClosed()?$formatter->format($businessHours->nextOpen($now)):null,
            'closingTime' => $businessHours->isOpen()?$formatter->format($businessHours->nextClose($now)):null,
            'usernames' => $usernames,
            'canDoOrdering'=>$canDoOrdering,
            'currentLanguage'=>$currentEnvLanguage,
            'showLanguagesSelector'=>env('ENABLE_MILTILANGUAGE_MENUS', false) && $restorant->localmenus()->count() > 1,
            'hasGuestOrders'=>count($previousOrderArray) > 0,
            'fields'=>[['class'=>'col-12', 'classselect'=>'noselecttwo', 'ftype'=>'select', 'name'=>'Table', 'id'=>'table_id', 'placeholder'=>'Select table', 'data'=>$tablesData, 'required'=>true]],
        ];
    }

    public function clear(Request $request)
    {
        if(isset($request->session_id)){
            $this->setSessionID($request->session_id);
        }

        //Get the client_id from address_id

        $oreder = new Order;
        $oreder->address_id = strip_tags($request->addressID);
        $oreder->restorant_id = strip_tags($request->restID);
        $oreder->client_id = auth()->user()->id;
        $oreder->driver_id = 2;
        $oreder->delivery_price = 3.00;
        $oreder->order_price = strip_tags($request->orderPrice);
        $oreder->comment = strip_tags($request->comment);
        $oreder->save();

        foreach (Cart::getContent() as $key => $item) {
            $oreder->items()->attach($item->id);
        }

        //Find first status id,
        Cart::clear();

        return redirect()->route('front')->withStatus(__('Cart clear.'));
    }

    /**
     * Create a new controller instance.

     *

     * @return void
     */
    public function remove(Request $request)
    {

        if(isset($request->session_id)){
            $this->setSessionID($request->session_id);
        }

        Cart::remove($request->id);
        if($request->has('orderId')) {
            if($request->orderId>0){
                $order = Order::findOrFail($request->orderId);
                foreach ($order->items()->get() as $key => $item) {
                    if($item->pivot->cart_item_id==$request->id){
                        $oi=Orderitems::findOrFail($item->pivot->id);
                        $oi->delete();
                    }
                }
            }
        }
       
        return response()->json([
            'status' => true,
            'errMsg' => '',
        ]);
    }

    /**
     * Makes general api resonse.
     */
    private function generalApiResponse()
    {
        return response()->json([
            'status' => true,
            'errMsg' => '',
        ]);
    }

    /**
     * Updates cart.
     */
    public function updateCartObser(Request $request){
        
        if(isset($request->session_id)){
            $this->setSessionID($request->session_id);
        }
        if ($request->has('observacion')) {
            Cart::update($request->id, ['observacion' =>$request->observacion]);
            if($request->orderid>0){
                $carItem=Cart::getContent();
                $order = Order::findOrFail($request->orderid);
                foreach ($order->items()->get() as $key => $item) {
                    if($item->pivot->cart_item_id==$request->id){
                       $oi=Orderitems::findOrFail($item->pivot->id);
                       $oi->item_observacion= $request->observacion;
                       $oi->update();
                    }
                }
            }
        }else{
            return json_encode(array('messeger'=>"falta observacion","error"=>true));
        }
    }
    
    private function updateCartQty($howMuch, $item_id,$orderId=0)
    {
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }

        Cart::update($item_id, ['quantity' => $howMuch]);
        $carItem=Cart::getContent();
        if($orderId>0){
            $order = Order::findOrFail($orderId);
            foreach ($order->items()->get() as $key => $item) {
                if($item->pivot->cart_item_id==$item_id){
                   $oi=Orderitems::findOrFail($item->pivot->id);
                   $oi->qty= $carItem[$item_id]->quantity;
                   $oi->update();
                   $totalRecaluclatedVAT =  $carItem[$item_id]->quantity* ($item->vat > 0?$item->pivot->variant_price * ($item->vat / 100):0);
                   $oi->vatvalue=$totalRecaluclatedVAT;
                   $oi->update();
                }
            }
        }
        return $this->generalApiResponse();
    }

    /**
     * Increase cart.
     */
    public function increase($id,$orderId=0)
    {
        if(isset($_GET['session_id'])){
            
            $this->setSessionID($_GET['session_id']);
        }
        return $this->updateCartQty(1, $id,$orderId);
    }

    /**
     * Decrese cart.
     */
    public function decrease($id,$orderId=0)
    {
        if(isset($_GET['session_id'])){
            $this->setSessionID($_GET['session_id']);
        }
        return $this->updateCartQty(-1, $id,$orderId);
    }
}
