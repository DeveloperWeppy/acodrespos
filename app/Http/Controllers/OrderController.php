<?php

namespace App\Http\Controllers;

use Cart;
use App\User;
use App\Order;
use App\Status;
use App\Coupons;
use App\Restorant;
use Carbon\Carbon;
use App\Categories;
use App\Models\Log;
use App\Models\Orderitems;
use App\Models\usersDriver;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\OrdersExport;
use App\Models\EncuestaOrden;
use App\Services\ConfChanger;
use App\Models\EncuestaClient;
use App\Models\SimpleDelivery;
use App\Models\CartStorageModel;
use willvincent\Rateable\Rating;
use Illuminate\Support\Facades\DB;
use App\Events\OrderAcceptedByAdmin;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Events\OrderAcceptedByVendor;
use Akaunting\Module\Facade as Module;
use App\Models\ConfigCuentasBancarias;
use Illuminate\Support\Facades\Cookie;
use App\Notifications\OrderNotification;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Database\Eloquent\Builder;
use App\Events\NewOrder as PusherNewOrder;
use App\Repositories\Orders\OrderRepoGenerator;

class OrderController extends Controller
{

    private function setSessionID($session_id){
        //We have session ID only from POS. So then use the CartDBStorageRepository
        config(['shopping_cart.storage' => \App\Repositories\CartDBStorageRepository::class]); 
        Cart::session($session_id);
    }


    public function migrateStatuses()
    {
        if (Status::count() < 13) {
            $statuses = ['Just created', 'Accepted by admin', 'Accepted by restaurant', 'Assigned to driver', 'Prepared', 'Picked up', 'Delivered', 'Rejected by admin', 'Rejected by restaurant', 'Updated', 'Closed', 'Rejected by driver', 'Accepted by driver'];
            foreach ($statuses as $key => $status) {
                Status::updateOrCreate(['name' => $status], ['alias' =>  str_replace(' ', '_', strtolower($status))]);
            }
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $this->migrateStatuses();

        $restorants = Restorant::where(['active'=>1])->get();
        
        $clients = User::role('client')->where(['active'=>1])->get();

        /*
        $drivers = auth()->user()->myDrivers();
        $driversData = [];
        foreach ($drivers as $key => $driver) {
            $driversData[$driver->id] = $driver->name;
        }
        */
        $times_delivered = array(
            '10 - 20' => '10 - 20',
            '20 - 30' => '20 - 30',
            '40 - 50' => '40 - 50',
            '60 - 70' => '60 - 70',
            '80 - 90' => '80 - 90',
        );
        $orders = Order::orderBy('created_at', 'desc')->whereNotNull('restorant_id');

        //Get client's orders
        if (auth()->user()->hasRole('client')) {

            $orders = $orders->where(['client_id'=>auth()->user()->id]);       

        } elseif (auth()->user()->hasRole('driver')) {
            $orders = $orders->where(['driver_id'=>auth()->user()->id]);
        } elseif (auth()->user()->hasRole('owner')) {
             
            //Change currency
            ConfChanger::switchCurrency(auth()->user()->restorant);

            $orders = $orders->where(['restorant_id'=>auth()->user()->restorant->id]);

        }elseif (auth()->user()->hasRole('staff')) {
             
            //Change currency
            ConfChanger::switchCurrency(auth()->user()->restaurant);

            $orders = $orders->where(['restorant_id'=>auth()->user()->restaurant_id])->where('employee_id', auth()->user()->id);
        }elseif (auth()->user()->hasRole('kitchen')) {
             
            //Change currency
            ConfChanger::switchCurrency(auth()->user()->restaurant);

            $orders = $orders->where(['restorant_id'=>auth()->user()->restaurant_id]);
        }elseif (auth()->user()->hasRole('manager_restorant')) {
             
            //Change currency
            ConfChanger::switchCurrency(auth()->user()->restaurant);

            $orders = $orders->where(['restorant_id'=>auth()->user()->restaurant_id]);
        }

        //FILTER BT RESTORANT
        if (isset($_GET['restorant_id'])) {
            $orders->where(['restorant_id'=>$_GET['restorant_id']]);
        }
        
        //If restorant owner, get his restorant orders only
        if (auth()->user()->hasRole('owner')) {
            //Current restorant id
            $restorant_id = auth()->user()->restorant->id;
            $orders->where(['restorant_id'=>$restorant_id]);
        }

        //BY CLIENT
        if (isset($_GET['client_id'])) {
            $orders->where(['client_id'=>$_GET['client_id']]);
        }

        //BY DRIVER
        if (isset($_GET['driver_id'])) {
            $orders->where(['driver_id'=>$_GET['driver_id']]);
        }

        //BY DATE FROM
        if (isset($_GET['fromDate']) && strlen($_GET['fromDate']) > 3) {
            $orders->whereDate('created_at', '>=', $_GET['fromDate']);
        }

        //BY DATE TO
        if (isset($_GET['toDate']) && strlen($_GET['toDate']) > 3) {
            $orders->whereDate('created_at', '<=', $_GET['toDate']);
        }

    
        //FILTER BT status
        if (isset($_GET['status_id'])) {

            $orders->where(DB::raw('(select status_id from order_has_status where order_id=orders.id order by status_id desc limit 1)'),'=',$_GET['status_id']);
            /*
            $orders->whereHas('laststatus', function($q){
                $q->where('status_id',"=", $_GET['status_id']);
            });
            */
        }else{
            $orders->whereHas('laststatus', function($q){
                $q->whereNotIn('status_id', [8,9]);
            });
        }

        

         //FILTER BT payment status
         if (isset($_GET['payment_status'])&&strlen($_GET['payment_status'])>2) {
            $orders->where('payment_status', $_GET['payment_status']);
        }

     
        //With downloaod
        if (isset($_GET['report'])) {
            $items = [];

            foreach ($orders->get() as $key => $order) {
                $item = [
                    'order_id'=>$order->id,
                    'restaurant_name'=>$order->restorant->name,
                    'restaurant_id'=>$order->restorant_id,
                    'created'=>$order->created_at,
                    'last_status'=>$order->status->pluck('alias')->last(),
                    'client_name'=>$order->client ? $order->client->name : '',
                    'client_id'=>$order->client ? $order->client_id : null,
                    'table_name'=>$order->table ? $order->table->name : '',
                    'table_id'=>$order->table ? $order->table_id : null,
                    'area_name'=>$order->table && $order->table->restoarea ? $order->table->restoarea->name : '',
                    'area_id'=>$order->table && $order->table->restoarea ? $order->table->restoarea->id : null,
                    'address'=>$order->address ? $order->address->address : '',
                    'address_id'=>$order->address_id,
                    'driver_name'=>$order->driver ? $order->driver->name : '',
                    'driver_id'=>$order->driver_id,
                    'order_value'=>$order->order_price_with_discount,
                    'order_delivery'=>$order->delivery_price,
                    'order_total'=>$order->delivery_price + $order->order_price_with_discount,
                    'payment_method'=>$order->payment_method,
                    'srtipe_payment_id'=>$order->srtipe_payment_id,
                    'order_fee'=>$order->fee_value,
                    'restaurant_fee'=>$order->fee,
                    'restaurant_static_fee'=>$order->static_fee,
                    'vat'=>$order->vatvalue,
                  ];
                array_push($items, $item);
            }

            return Excel::download(new OrdersExport($items), 'orders_'.time().'.xlsx');
        }

        $orders = $orders->paginate(10);

        $estados =  [
            2 => "Accepted by admin",
            3 => "Accepted by restaurant",
            4 => "Assigned to driver",
            11 => "Closed",
            7 => "Delivered",
            1 => "Just created",
            6 => "Picked up",
            5 => "Prepared",
            8 => "Rejected by admin",
            9 => "Rejected by restaurant",
            10 => "Updated",
        ];
    

        //Status::pluck('name','id')->toArray()
        return view('orders.index', [
            'statuses'=>$estados,
            'orders' => $orders,
            'restorants'=>$restorants,
            'fields'=>[['class'=>'col-12', 'class'=>'', 'ftype'=>'input', 'name'=>'Nombre del Conductor', 'id'=>'nom', 'placeholder'=>'Nombre del Conductor', 'data'=>null, 'required'=>true],
            ['class'=>'col-12', 'class'=>'', 'ftype'=>'input', 'name'=>'Tel??fono del Conductor', 'id'=>'tel', 'placeholder'=>'Tel??fono del Conductor', 'data'=>null, 'required'=>true],
            ['class'=>'', 'classselect'=>'noselecttwo', 'ftype'=>'select', 'name'=>'Tiempo estimado de Entrega(minutos)', 'id'=>'time_delivered', 'placeholder'=>'Seleccione Tiempo en Minutos', 'data'=>$times_delivered, 'required'=>true]],
            'clients'=>$clients,
            'parameters'=>count($_GET) != 0,
        ]);
        // 'drivers'=>$drivers,
    }

    // 'fields'=>[['class'=>'col-12', 'classselect'=>'noselecttwo', 'ftype'=>'select', 'name'=>'Driver', 'id'=>'driver', 'placeholder'=>'Assign Driver', 'data'=>$driversData, 'required'=>true]],


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }
    
    private function toRideMobileLike(Request $request){
        //Data
        $driver=User::findOrFail($request->driver_id);
        $vendor_id = $driver->restaurant_id;
        
       $requestData=[
           'issd'=>true,
           'vendor_id'   => $vendor_id,
           'driver_id' => $request->driver_id,
           'delivery_method'=> "delivery",
           'payment_method'=> "cod",
           'address_id'=>null,
           'pickup_address'=>$request->pickup_address,
           'pickup_lat'=>$request->pickup_lat,
           'pickup_lng'=>$request->pickup_lng,
           'delivery_address'=>$request->delivery_address,
           'delivery_lat'=>$request->delivery_lat,
           'delivery_lng'=>$request->delivery_lng,
           "timeslot"=>"",
           "items"=>[],
           "comment"=>$request->comment,
           "phone"=>$request->phoneclient,
       ];
       return new Request($requestData);

    }

    private function toMobileLike(Request $request){
        //Find vendor id
        $vendor_id = null;
        foreach (Cart::getContent() as $key => $item) {
            $vendor_id = $item->attributes->restorant_id;
        }
        $restorant = Restorant::findOrFail($vendor_id);

        //Organize the item
        $items=[];
        foreach (Cart::getContent() as $key => $item) {
            $extras=[];
            foreach ($item->attributes->extras as $keyExtra => $extra_id) {
                array_push($extras,array('id'=>$extra_id));
            }
            array_push($items,array(
                "id"=>$item->attributes->id,
                "qty"=>$item->quantity,
                "variant"=>$item->attributes->variant,
                "extrasSelected"=>$extras
            ));
        }


        //stripe token
        $stripe_token=null;
        if($request->has('stripePaymentId')){
            $stripe_token=$request->stripePaymentId;
        }

        //Custom fields
        $customFields=[];
        if($request->has('custom')){
            $customFields=$request->custom;
        }

        
       

        //DELIVERY METHOD
        //Default - pickup - since available everywhere
        $delivery_method="pickup";
        
        //Delivery method - deliveryType - ft
        if($request->has('deliveryType')){
            $delivery_method=$request->deliveryType;
        }else if($restorant->can_pickup == 0 && $restorant->can_deliver == 1){
            $delivery_method="delivery";
        }

        //Delivery method  - dineType - qr
        if($request->has('dineType')){
            $delivery_method=$request->dineType;
        }



        //In case it is QR, and there is no dineInType, and pickup is diabled, it is dine in
        if(config('app.isqrsaas')&&!$request->has('dineType')&&!(config('settings.is_whatsapp_ordering_mode')||config('settings.is_agris_mode'))){
            $delivery_method='dinein';
        }
        //takeaway is pickup
        if($delivery_method=="takeaway"){
            $delivery_method="pickup";
        }

        //Table id
        $table_id=null;
        if($request->has('table_id')){
            $table_id=$request->table_id;
        }

         //Phone 
         $phone=null;
         if($request->has('phone')){
             $phone=$request->phone;
         }

        //Delivery area
        $deliveryAreaId=$request->has('delivery_area')?$request->delivery_area:null;
        if($deliveryAreaId){
            //Set this in custom field
            $deliveryAreaName="";
            $deliveryAreaElement=SimpleDelivery::find($request->delivery_area);
            if($deliveryAreaElement){
                $deliveryAreaName=$deliveryAreaElement->name;
            }
            $customFields['delivery_area_name']=$deliveryAreaName;
        }

        $requestData=[
            'vendor_id'   => $vendor_id,
            'delivery_method'=> $delivery_method,
            'payment_method'=> $request->paymentType?$request->paymentType:"cod",
            'address_id'=>$request->addressID,
            "timeslot"=>$request->timeslot,
            "items"=>$items,
            "comment"=>$request->comment,
            "stripe_token"=>$stripe_token,
            "dinein_table_id"=>$table_id,
            "phone"=>$phone,
            "customFields"=>$customFields,
            "deliveryAreaId"=> $deliveryAreaId,
            "coupon_code"=> $request->has('coupon_code')&&strlen($request->coupon_code)>3?$request->coupon_code:null
        ];



        return new Request($requestData);
    }

    public function store(Request $request){
        //dd($request);
        //Convert web request to mobile like request
        if(config('app.issd',false)||$request->has('issd')){
            //Web ride
            $mobileLikeRequest=$this->toRideMobileLike($request);
        }else{
            //Web order
            $mobileLikeRequest=$this->toMobileLike($request);
        }

        
        //dd($mobileLikeRequest->delivery_method);

        //Data
        $vendor_id =  $mobileLikeRequest->vendor_id;
        $expedition= $mobileLikeRequest->delivery_method;
        $hasPayment= $mobileLikeRequest->payment_method!="cod";
        $isStripe= $mobileLikeRequest->payment_method=="stripe";
        if ($request->hasFile('img_evidencia')) {
            $mobileLikeRequest->img_evidencia=$request->img_evidencia;
        }
        if($request->has('id_account_bank')) {
            $mobileLikeRequest->id_account_bank=$request->id_account_bank;
        }
        $vendorHasOwnPayment=null;
        if(config('settings.social_mode')||config('app.issd',false)){
            //Find the vendor, and check if he has payment
        
            $vendor=Restorant::findOrFail($mobileLikeRequest->vendor_id);

            //Payment methods
            foreach (Module::all() as $key => $module) {
                if($module->get('isPaymentModule')){
                    if($vendor->getConfig($module->get('alias')."_enable","false")=="true"){
                        $vendorHasOwnPayment=$module->get('alias');
                    }
                }
            }

            if($vendorHasOwnPayment==null){
                $hasPayment=false;
            }else{
                //Since v3, don't auto select payment model, show all the  options to  user
                $vendorHasOwnPayment="all";
            }
        }

        //Repo Holder
        $orderRepo=OrderRepoGenerator::makeOrderRepo($vendor_id,$mobileLikeRequest,$expedition,$hasPayment,$isStripe,false,$vendorHasOwnPayment);

        //Proceed with validating the data
        $validator=$orderRepo->validateData();
        if ($validator->fails()) { 
            notify()->error($validator->errors()->first());
            return $orderRepo->redirectOrInform(); 
        }

        //Proceed with making the order
        $validatorOnMaking=$orderRepo->makeOrder();
        if ($validatorOnMaking->fails()) { 
            notify()->error($validatorOnMaking->errors()->first()); 
            return $orderRepo->redirectOrInform(); 
        }

        return $orderRepo->redirectOrInform();
    }

    public function statusitemorder(Request $request)
    {
        $function = $this->getIpLocation();
        $class_status = $text_status = '';
        $status = 'cocina';

        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = $request->id;

            $item = DB::table('order_has_items')
            ->where('id', $id)
            ->first();
            $active = ($item->item_status == 'cocina') ? 'servicio' : 'cocina';

            $actualiza = DB::table('order_has_items')
              ->where('id', $id)
              ->update(['item_status' => $active]);

            Log::create([
                'user_id' => Auth::user()->id,
                'ip' => $request->ip(),
                'module' => 'ORDEN',
                'submodule' => 'PRODUCTO EN COCINA',
                'action' => 'Actualizaci??n',
                'detail' => 'Se actualiz?? el estado al producto por, -'.$active. '- de la orden #' .$item->order_id,
                'country' => $function->country,
                'city' =>$function->city,
                'lat' =>$function->lat,
                'lon' =>$function->lon,
            ]);
            $order = Order::findOrFail($item->order_id);
            if ($order->employee_id != null) {
                if(Auth::user()->id!=$order->employee_id){
                    $producto= DB::table('items')->where('id',$item->item_id)->get();
                    $employee = User::findOrFail($order->employee_id);
                    $employee->notify(new OrderNotification($order, $active,null,$producto[0]->name));
                }
            }
            if($order->client_id != null){
                
                
                $res=new OrderNotification($order,$active);
               
                $order->client->notify( $res);
               
                
            }
            
            if ($actualiza == 1) {
             
                $class_status = $active == 'servicio' ? "btn-outline-success btn-sm" : "btn-outline-warning btn-sm";
                $text_status = $active == 'servicio' ? "Servicio" : "Cocina";
                $status = 'servicio';
            } 
            
        }

        echo json_encode(array("status" => $status, "class_status" => $class_status, "text_status" => $text_status));
    }
    public function statusitemorder2($id)
    {

        $class_status = $text_status = '';
        $status = 'cocina';
        $_POST['id']=$id;
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $item = DB::table('order_has_items')
            ->where('id', $id)
            ->first();
            $active = ($item->item_status == 'cocina') ? 'servicio' : 'cocina';

            $actualiza = DB::table('order_has_items')
              ->where('id', $id)
              ->update(['item_status' => $active]);

            $order = Order::findOrFail($item->order_id);
            if(auth()->user()->id!=$order->employee_id){
                $producto= DB::table('items')->where('id',$item->item_id)->get();
                $employee = User::findOrFail($order->employee_id);
                $employee->notify(new OrderNotification($order, $active,null,$producto[0]->name));
            }
            
            if ($actualiza == 1) {
                $class_status = ($active == 'servicio') ? "btn-outline-success btn-sm" : "btn-outline-warning btn-sm";
                $text_status = ($active == 'servicio') ? "Servicio" : "Cocina";
                $status = 'servicio';
            } 
            
        }

        echo json_encode(array("status" => $status, "class_status" => $class_status, "text_status" => $text_status));
    }
    public function orderLocationAPI(Order $order)
    {
        if ($order->status->pluck('alias')->last() == 'picked_up') {
            return response()->json(
                [
                    'status'=>'tracing',
                    'lat'=>$order->lat,
                    'lng'=>$order->lng,
                    ]
            );
        } else {
            return response()->json(['status'=>'not_tracing']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {

        $driver = usersDriver::where('order_id','=',$order->id)->orderBy('id','desc')->get();


        //Do we have pdf invoice
        $pdFInvoice=Module::has('pdf-invoice');

        //load questions of poll
        $questions = EncuestaOrden::whereNull('deleted_at')->get();
        //Change currency
        ConfChanger::switchCurrency($order->restorant);

        //Change language
        ConfChanger::switchLanguage($order->restorant);

        if($order->restorant){
             //Set config based on restaurant
            config(['app.timezone' => $order->restorant->getConfig('time_zone',config('app.timezone'))]);
        }

        $driversData = [];
        /*
        $drivers =auth()->user()->myDrivers();
        foreach ($drivers as $key => $driver) {
            $driversData[$driver->id] = $driver->name;
        }
        */
        
        if (auth()->user()->hasRole('client') && auth()->user()->id == $order->client_id ||
            auth()->user()->hasRole('owner') && auth()->user()->id == $order->restorant->user->id ||
            auth()->user()->hasRole('manager_restorant') && auth()->user()->restaurant_id == $order->restorant->id ||
            auth()->user()->hasRole('staff') && auth()->user()->restaurant_id == $order->restorant->id ||
            auth()->user()->hasRole('kitchen') && auth()->user()->restaurant_id == $order->restorant->id ||
                auth()->user()->hasRole('driver') && auth()->user()->id == $order->driver_id || auth()->user()->hasRole('admin')
            ) {


                $orderModules=[];
                foreach (Module::all() as $key => $module) {
                    if($module->get('isOrderModule')){
                        array_push($orderModules,$module->get('alias'));
                    }
                }
                $banck=array();
            
            if($order->id_account_bank!=""){
                $bank2 = ConfigCuentasBancarias::find($order->id_account_bank);
                $order->name_bank =$bank2->name_bank;
                $order->number_account =$bank2->number_account;
            }
            return view('orders.show', [
                'order'=>$order,
                'questions' => $questions,
                'pdFInvoice'=>$pdFInvoice,
                'custom_data'=>$order->getAllConfigs(),
                'statuses'=>Status::pluck('name', 'id'), 
                'drivers'=>$driver,
                'orderModules'=>$orderModules,
                'fields'=>[['class'=>'col-12', 'class'=>'', 'ftype'=>'input', 'name'=>'Nombre del Conductor', 'id'=>'nom', 'placeholder'=>'Nombre del Conductor', 'data'=>null, 'required'=>true],['class'=>'col-12', 'class'=>'', 'ftype'=>'input', 'name'=>'Tel??fono del Conductor', 'id'=>'tel', 'placeholder'=>'Tel??fono del Conductor', 'data'=>null, 'required'=>true]],


            ]);

            //                'fields'=>[['class'=>'col-12', 'classselect'=>'noselecttwo', 'ftype'=>'select', 'name'=>'Driver', 'id'=>'driver', 'placeholder'=>'Assign Driver', 'data'=>$driversData, 'required'=>true]],

        } else {
            return redirect()->route('orders.index')->withStatus(__('No Access.'));
        }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the order item count
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,Order $order)
    {
        $function = $this->getIpLocation();
        if (auth()->user()->hasRole('owner') && auth()->user()->id == $order->restorant->user->id ||
            auth()->user()->hasRole('staff') && auth()->user()->restaurant_id == $order->restorant->id || 
            auth()->user()->hasRole('admin')
            ) {
            if($request->has('delivery_pickup_interval')) {
                $interEntrega=explode("_",$order->delivery_pickup_interval);
                if(isset($interEntrega[1])){
                    $aumentartiempo=intval($interEntrega[1])+$request->delivery_pickup_interval;
                    if($aumentartiempo>0 && $aumentartiempo>intval($interEntrega[0])){
                        $order->delivery_pickup_interval=$interEntrega[0]."_".$aumentartiempo;
                        $order->update();
                        $order->client->notify(new OrderNotification($order,11));

                        Log::create([
                            'user_id' => Auth::user()->id,
                            'ip' => $request->ip(),
                            'module' => 'ORDEN',
                            'submodule' => '',
                            'action' => 'Actualizaci??n',
                            'detail' => 'Se actualiz?? la orden, #' .$order->id,
                            'country' => $function->country,
                            'city' =>$function->city,
                            'lat' =>$function->lat,
                            'lon' =>$function->lon,
                        ]);
                    }
                }
                
            }
               

                //Don't allow all 0 qty
                $zeroQty=0;
                foreach ($order->items()->get() as $key => $item) {
                    if($item->pivot->id==$request->pivot_id){
                        
                        if($request->item_qty.""=="0"){
                            $zeroQty++; 
                        }
                    }else{
                        if($item->pivot->qty==0){
                            $zeroQty++;
                        }
                    }
                }
                if($zeroQty==$order->items()->count()){
                    return redirect()->route('orders.show',$order->id)->withStatus(__('Can not set all qty to 0. You can reject order instead'));
                }
                
                //Directly find the pivot
                foreach ($order->items()->get() as $key => $item) {
                    if($item->pivot->id==$request->pivot_id){
                        $oi=Orderitems::findOrFail($item->pivot->id);
                        $oi->qty=$request->item_qty;
                        $oi->update();
                        Log::create([
                            'user_id' => Auth::user()->id,
                            'ip' => $request->ip(),
                            'module' => 'ORDEN',
                            'submodule' => 'PRODUCTO',
                            'action' => 'Actualizaci??n',
                            'detail' => 'Se actualiz?? el producto, ' .$item->name. ' de la orden #'.$order->id,
                            'country' => $function->country,
                            'city' =>$function->city,
                            'lat' =>$function->lat,
                            'lon' =>$function->lon,
                        ]);
                        //$order->items()->updateExistingPivot($item, array('qty' => $request->item_qty), false);
                        $totalRecaluclatedVAT = $request->item_qty * ($item->vat > 0?$item->pivot->variant_price * ($item->vat / 100):0);
                        
                        $oi->vatvalue=$request->$totalRecaluclatedVAT;
                        $oi->update();
                        //$order->items()->updateExistingPivot($item, array('vatvalue' => $totalRecaluclatedVAT), false);
                    }
                }
                
                 //After we have updated the list of items, we need to update the order price
                $order_price=0;
                $total_order_vat=0;
                foreach ($order->items()->get() as $key => $item) {
                    $order_price+=$item->pivot->qty*$item->pivot->variant_price;
                    $total_order_vat+=$item->pivot->vatvalue;
                }
                $order->order_price=$order_price;
                $order->vatvalue=$total_order_vat;
                $order->update();

                Log::create([
                    'user_id' => Auth::user()->id,
                    'ip' => $request->ip(),
                    'module' => 'ORDEN',
                    'submodule' => '',
                    'action' => 'Actualizaci??n',
                    'detail' => 'Se actualiz?? el precio de la orden, #' .$order->id . ' a ' .$order_price,
                    'country' => $function->country,
                    'city' =>$function->city,
                    'lat' =>$function->lat,
                    'lon' =>$function->lon,
                ]);
                //If this order have discount, recaluclate deduct, it can be percentage based
                if(strlen($order->coupon)>0){
                    $coupon = Coupons::where(['code' => $order->coupon])->get()->first();
                    if($coupon){
                        $deduct=$coupon->calculateDeduct($order->order_price);
                        if($deduct){
                            $order->discount=$deduct;
                        }
                    }
                }
                $order->update();
                
                return redirect()->route('orders.show',$order->id)->withStatus(__('Order updated.'));
                //You can update the order
            }else{
                return redirect()->route('orders.show',$order->id)->withStatus(__('No Access.'));
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function liveapi()
    {

        if (auth()->user()->hasRole('client')) {
            abort(404,'Not allowed as client');
        }

        //Today only
        $orders = Order::where('created_at', '>=', Carbon::today())->orderBy('created_at', 'desc');
        $resto=auth()->user()->restorant;
        //If owner, only from his restorant
        if (auth()->user()->hasRole('owner')||auth()->user()->hasRole('kitchen')) {
            
            
            $orders = $orders->where(['restorant_id'=>$resto->id]);
            
            //Change currency
            ConfChanger::switchCurrency($resto);

            //Set config based on restaurant
            config(['app.timezone' => $resto->getConfig('time_zone',config('app.timezone'))]);

            //Change language
            //ConfChanger::switchLanguage($order->restorant);
        }else if(auth()->user()->hasRole('staff')){
            $orders = $orders->where(['restorant_id'=>$resto->id])->where('employee_id', auth()->user()->id);
            
            //Change currency
            ConfChanger::switchCurrency($resto);

            //Set config based on restaurant
            config(['app.timezone' => $resto->getConfig('time_zone',config('app.timezone'))]);
        }
        $orders = $orders->with(['status', 'client', 'restorant', 'table.restoarea'])->get()->toArray();


        $newOrders = [];
        $acceptedOrders = [];
        $doneOrders = [];

        $items = [];
        $arrayCategories = json_decode( json_encode( Categories::where('restorant_id', $resto->id)->get() ), true );
        $arrayArea= json_decode( json_encode(DB::table('area_kitchens')->where('restorant_id',$resto->id)->get()), true );
       
        foreach ($orders as $key => $order) {
            $arrayareac=array();
            $orderf = Order::findOrFail($order['id']);
            foreach ($orderf->items()->get() as $key => $item2) {
                $found_key = array_search($item2->category_id, array_column($arrayCategories, 'id'));
                if($found_key!==false){
                    $found_key2 = array_search($arrayCategories[$found_key]['areakitchen_id'], array_column($arrayArea, 'id'));
                    if($found_key2!==false){
                        $found_key3 = array_search($arrayArea[$found_key2]['id'], array_column($arrayareac, 'id'));
                        if($found_key3===false){
                            array_push($arrayareac,array('id'=>$arrayArea[$found_key2]['id'],'name'=>$arrayArea[$found_key2]['name'],'colorarea'=>$arrayArea[$found_key2]['colorarea']));
                        }
                    }
                }
            }
            $client="";
            if(config('app.isft')){
                //$client=$order['client']['name'];

                if($order['table']&&$order['table']['restoarea']&&$order['table']['restoarea']['name']&&$order['table']['name']){
                    $client=$order['table']['restoarea']['name'].' - '.$order['table']['name'];
                }else if($order['table']&&$order['table']['name']){
                    $client=$order['table']['name'];
                }else{
                    //If the order was made by a registered user, returns his name
                    if(isset($order['client']['name'])){
                        $client=$order['client']['name'];
                    }else{
                        $client="";
                    }
                }
            }else{
                if(!config('settings.is_whatsapp_ordering_mode')){
                    //QR
                    if($order['table']&&$order['table']['restoarea']&&$order['table']['restoarea']['name']&&$order['table']['name']){
                        $client=$order['table']['restoarea']['name'].' - '.$order['table']['name'];
                    }else if($order['table']&&$order['table']['name']){
                        $client=$order['table']['name'];
                    }
                }else{
                    //WhatsApp
                    $client=$order['phone'];
                }
            }
            array_push($items, [
                'id'=>$order['id'],
                'restaurant_name'=>$order['restorant']['name'],
                'last_status'=>count($order['status']) > 0 ? __($order['status'][count($order['status']) - 1]['name']) : 'Just created',
                'last_status_id'=>count($order['status']) > 0 ? $order['status'][count($order['status']) - 1]['pivot']['status_id'] : 1,
                'time'=>Carbon::create($order['updated_at'])->locale(config('app.locale'))->isoFormat('LLLL'),
                'client'=>$client,
                'link'=>'/orders/'.$order['id'],
                'price'=>money($order['order_price'], config('settings.cashier_currency'), config('settings.do_convertion')).'',
                'areas'=>$arrayareac
            ]);
        }

        //----- ADMIN ------
        if (auth()->user()->hasRole('admin')) {
            foreach ($items as $key => $item) {
                //Box 1 - New Orders
                //Today orders that are just created ( Needs approvment or rejection )
                //Box 2 - Accepted
                //Today orders approved by Restaurant , or by admin( Needs assign to driver )
                //Box 3 - Done
                //Today orders assigned with driver, or rejected
                if ($item['last_status_id'] == 1) {
                    $item['pulse'] = 'blob green';
                    array_push($newOrders, $item);
                } elseif ($item['last_status_id'] == 2 || $item['last_status_id'] == 3) {
                    $item['pulse'] = 'blob orangestatic';
                    if ($item['last_status_id'] == 3) {
                        $item['pulse'] = 'blob orange';
                    }
                    array_push($acceptedOrders, $item);
                } elseif ($item['last_status_id'] > 3) {
                    $item['pulse'] = 'blob greenstatic';
                    if ($item['last_status_id'] == 9 || $item['last_status_id'] == 8) {
                        $item['pulse'] = 'blob redstatic';
                    }
                    array_push($doneOrders, $item);
                }
            }
        }

        //----- Restaurant ------
        if (auth()->user()->hasRole('owner')||auth()->user()->hasRole('manager_restorant') ||auth()->user()->hasRole('staff')||auth()->user()->hasRole('kitchen')) {
            foreach ($items as $key => $item) {

                
                //Box 1 - New Orders
                //Today orders that are approved by admin ( Needs approvment or rejection )
                //Box 2 - Accepted
                //Today orders approved by Restaurant ( Needs change of status to done )
                //Box 3 - Done
                //Today completed or rejected
                $last_status = $item['last_status_id'];
                if ($last_status == 2 || $last_status == 10 || ($item['last_status_id'] == 1)) {
                    $item['pulse'] = 'blob green';
                    array_push($newOrders, $item);
                } elseif ($last_status == 3 || $last_status == 4 || $last_status == 5) {
                    $item['pulse'] = 'blob orangestatic';
                    if ($last_status == 3) {
                        $item['pulse'] = 'blob orange';
                    }
                    array_push($acceptedOrders, $item);
                } elseif ($last_status > 5 && $last_status != 8) {
                    $item['pulse'] = 'blob greenstatic';
                    if ($last_status == 9 || $last_status == 8) {
                        $item['pulse'] = 'blob redstatic';
                    }
                    array_push($doneOrders, $item);
                }
            }
        }

        $toRespond = [
                'neworders'=>$newOrders,
                'accepted'=>$acceptedOrders,
                'done'=>$doneOrders,
            ];

        return response()->json($toRespond);
    }

    public function live()
    {
        return view('orders.live');
    }

    public function autoAssignToDriver(Order $order)
    {
        //The restaurant id
        $restaurant_id = $order->restorant_id;
        if($order->restorant->self_deliver.""=="1"){
            //Don't use self assign when restaurant deliver on their own
            return null;
        }

        //1. Get all the working drivers, where active and working
        $theQuery = User::role('driver')->where(['active'=>1, 'working'=>1])->whereNull('restaurant_id');

        //2. Get Drivers with their assigned order, where payment_status is unpaid yet, this order is still not delivered and not more than 1
        $theQuery = $theQuery->whereHas('driverorders', function (Builder $query) {
            $query->where('payment_status', '!=', 'paid')->where('created_at', '>=', Carbon::today());
        }, '<=', 1);

        //Get Restaurant lat / lng
        $restaurant = Restorant::findOrFail($restaurant_id);
        $lat = $restaurant->lat;
        $lng = $restaurant->lng;

        //3. Sort drivers by distance from the restaurant
        $driversWithGeoIDS = $this->scopeIsWithinMaxDistance($theQuery, $lat, $lng, config('settings.driver_search_radius'), 'users')->pluck('id')->toArray();

        //4. The top driver gets the order
        if (count($driversWithGeoIDS) == 0) {
            //No driver found -- this will appear in  the admin list also in the list of free order so driver can get an order
        } else {
            //Driver found
            $order->driver_id = $driversWithGeoIDS[0];
            $order->update();
            $order->status()->attach([4 => ['comment'=>'System', 'user_id' => $driversWithGeoIDS[0]]]);

            //Now increment the driver orders
            $theDriver = User::findOrFail($order->driver_id);
            $theDriver->numorders = $theDriver->numorders + 1;
            $theDriver->update();
        }
    }

    public function updateStatus($alias, Order $order,$motivo="")
    {
        $function = $this->getIpLocation();
        if($alias=="accepted_by_restaurant"){
            if((intval($order->restorant->current_consecutive)>intval($order->restorant->final_consecutive)) && $order->consecutive=="" ){
                return redirect()->route('orders.index')->with("error","! Error Actualiza el  consecutivo de factura");
            }
            if($order->consecutive=="" && (intval($order->restorant->current_consecutive)<=intval($order->restorant->final_consecutive))){
                $order->prefix_consecutive=$order->restorant->prefix_consecutive;
                $order->consecutive=$order->restorant->current_consecutive;
                $restaurantp = Restorant::findOrFail($order->restorant_id);
                $restaurantp->current_consecutive=intval($order->restorant->current_consecutive)+1;
                $restaurantp->update();
            }
         }
        //-- asignar conductor con id desde la tabla de users
        /*
        if (isset($_GET['driver'])) {
            $order->driver_id = $_GET['driver'];
            $order->update();

            //Now increment the driver orders
            $theDriver = User::findOrFail($order->driver_id);
            $theDriver->numorders = $theDriver->numorders + 1;
            $theDriver->update();
        }
        */
    
        //Verifica si la orden aun tienen productos pendientes en cocina

        if($alias=="prepared"){

            $datos = auth()->user()->restorant->has_kitchen;
        
            if($datos==1){
                if(auth()->user()->hasRole('owner') || auth()->user()->hasRole('manager_restorant') || auth()->user()->hasRole('kitchen') || auth()->user()->hasRole('staff') ) {

                    $numPre = DB::table('order_has_items')->where('order_id',$order->id)->where('item_status','cocina')->count();

                    if($numPre>0){
                        return redirect()->route('orders.show', ['order'=>$order])->with('error','Aun hay productos en cocina');
                    }
                }
            }
        }

       
        
        //asignar conductor con campo abierto
        if(isset($_GET['nom'],$_GET['tel'],$_GET['time_delivered'])){
            $usersDriver = usersDriver::updateOrCreate(
                ['order_id' => $order->id],
                ['name' => strip_tags($_GET['nom']),
                'phone' => strip_tags($_GET['tel']),
                'time_delivered' => strip_tags($_GET['time_delivered'])
                ]
            );
            Log::create([
                'user_id' => Auth::user()->id,
                'ip' => $function->ip,
                'module' => 'DOMICILIARIO',
                'submodule' => 'PEDIDO',
                'action' => 'Registro',
                'detail' => 'Se asign?? el domiciliario, ' .strip_tags($_GET['nom']). ' a la orden #'.$order->id,
                'country' => $function->country,
                'city' =>$function->city,
                'lat' =>$function->lat,
                'lon' =>$function->lon,
            ]);
        }

        if (isset($_GET['time_to_prepare'])) {
            $order->time_to_prepare = $_GET['time_to_prepare'];
            $order->update();
            Log::create([
                'user_id' => Auth::user()->id,
                'ip' => $function->ip,
                'module' => 'ORDEN',
                'submodule' => 'TIEMPO DE PREPARACI??N',
                'action' => 'Registro',
                'detail' => 'Se registr?? , ' .strip_tags($_GET['time_to_prepare']). ' min. de preparaci??n a la orden #'.$order->id,
                'country' => $function->country,
                'city' =>$function->city,
                'lat' =>$function->lat,
                'lon' =>$function->lon,
            ]);
        }

        $status_id_to_attach = Status::where('alias', $alias)->value('id');

        //Check access before updating
        /**
         * 1 - Super Admin
         * accepted_by_admin
         * assigned_to_driver
         * rejected_by_admin.
         *
         * 2 - Restaurant
         * accepted_by_restaurant - 3
         * prepared
         * rejected_by_restaurant
         * picked_up
         * delivered
         *
         * 3 - Driver
         * picked_up
         * delivered
         */
        //

        $rolesNeeded = [
            'accepted_by_admin'=>'admin',
            'assigned_to_driver'=>['admin','owner'],
            'rejected_by_admin'=>'admin',
            'accepted_by_restaurant'=>['owner', 'staff', 'kitchen'],
            'prepared'=>['owner', 'staff', 'kitchen'],
            'rejected_by_restaurant'=>['owner', 'staff'],
            'picked_up'=>['driver', 'owner', 'staff'],
            'delivered'=>['driver', 'owner', 'staff'],
            'c'=>['owner', 'staff'],
            'accepted_by_driver'=>['driver','owner'],
            'rejected_by_driver'=>['driver']
        ];

        if (! auth()->user()->hasRole($rolesNeeded[$alias])) {
            abort(403, 'Acci??n no autorizada. No tienes el rol apropiado');
        }

        //For owner - make sure this is his order
        if (auth()->user()->hasRole('owner')) {
            //This user is owner, but we must check if this is order from his restaurant
            if (auth()->user()->id != $order->restorant->user_id) {
                abort(403, 'Acci??n no autorizada. No tienes el rol apropiado');
            }
        }

        if (auth()->user()->hasRole('staff')) {
            //This user is owner, but we must check if this is order from his restaurant
            if (auth()->user()->restaurant_id != $order->restorant->id) {
                abort(403, 'Acci??n no autorizada. No tienes el rol apropiado');
            }
        }

        //For driver - make sure he is assigned to this order
        if (auth()->user()->hasRole('driver')) {
            //This user is owner, but we must check if this is order from his restaurant
            if (auth()->user()->id != $order->driver->id) {
                abort(403, 'Acci??n no autorizada. No tienes el rol apropiado');
            }
        }

        /**
         * IF status
         * Accept  - 3
         * Prepared  - 5
         * Rejected - 9.
         */

        
        if (config('app.isft')&&$order->client) {

            

            if ($status_id_to_attach.'' == '3' || $status_id_to_attach.'' == '5' || $status_id_to_attach.'' == '9' || $status_id_to_attach.'' == '7' || $status_id_to_attach.'' == '4') {
                
                $res=new OrderNotification($order, $status_id_to_attach);
               
                $order->client->notify( $res);
                if( $order->getExpeditionType()=="Recogida" && strlen($order->client->phone )<14 && strlen($order->client->phone )>9 && $status_id_to_attach.'' == '5'){
                    if(substr($order->client->phone , 0, 1) === "+"){
                        $resmsm=$this->envioSms(substr($order->client->phone ,1,strlen($order->client->phone )),"Tu pedido #".$order->id. " esta listo,acercate al mostrador");
                    }else{
                       if(strlen($order->client->phone)==12){
                         $resmsm=$this->envioSms($order->client->phone,"Tu pedido #".$order->id. " esta listo,acercate al mostrador");
                       }
                    }
                }
            }

            if ($status_id_to_attach.'' == '4') {
               //$order->driver->notify(new OrderNotification($order, $status_id_to_attach));
            }
        }
        if(isset($order->employee_id)){
              
                $employee = User::findOrFail($order->employee_id);
                $employee->notify(new OrderNotification($order, $status_id_to_attach));
        }

        //Picked up - start tracing
        /*
        if ($status_id_to_attach.'' == '6') {
            $order->lat = $order->restorant->lat;
            $order->lng = $order->restorant->lng;
            $order->update();
        }
        */

        if (config('app.isft') && $alias.'' == 'delivered') {
            $order->payment_status = 'paid';
            $order->update();
        }

        if (config('app.isqrsaas') && $alias.'' == 'closed') {
            $order->payment_status = 'paid';
            $order->update();
        }

        /*
        if (config('app.isft')) {
            //When orders is accepted by restaurant, auto assign to driver
            if ($status_id_to_attach.'' == '3') {
                //ed :2
                if (config('settings.allow_automated_assign_to_driver')) {
                    $this->autoAssignToDriver($order);
                }
            }
        }
        */
        $comment="";
        if($alias=="rejected_by_restaurant" && $motivo!=""){
           $comment=$motivo;
           $sessiocart= explode("_", $order->cart_storage_id);
           if($order->cart_storage_id!=""){
                if(count($sessiocart)>1){
                    $this->setSessionID($sessiocart[0]);
                    Cart::clear();
                }
           }       
        }
        $order->status()->attach([$status_id_to_attach => ['comment'=>$comment, 'user_id' => auth()->user()->id]]);
        //Dispatch event
        if($alias=="accepted_by_restaurant"){
           //ed: 3 
           OrderAcceptedByVendor::dispatch($order);

        }
        if($alias=="accepted_by_admin"){
            //IN FT send email
            if (config('app.isft')) {
                $order->restorant->user->notify((new OrderNotification($order))->locale(strtolower(config('settings.app_locale'))));
            }
            
            OrderAcceptedByAdmin::dispatch($order);
        }
        $text = '';
        if ($alias=="accepted_by_restaurant") {
            $text = 'Aceptado por el restaurante';
        } else if ($alias=="assigned_to_driver"){
            $text = 'Asignado al domiciliario';
        } else if ($alias=="prepared"){
            $text = 'Preparado';
        } else if ($alias=="picked_up"){
            $text = 'Recogido';
        } else if ($alias=="delivered"){
            $text = 'Entregado';
        } else if ($alias=="rejected_by_admin"){
            $text = 'Rechazado por el administrador';
        } else if ($alias=="rejected_by_restaurant"){
            $text = 'Rechazado por el restaurante';
        } else if ($alias=="updated"){
            $text = 'Actualizado';
        } else if ($alias=="closed"){
            $text = 'Cerrado';
        }
        
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $function->ip,
            'module' => 'ORDEN',
            'submodule' => 'ACTUALIZACI??N DE ESTADO',
            'action' => 'Actualizaci??n',
            'detail' => 'Se actualiz?? la orden , #' .$order->id . ' a '.$text,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);

      return redirect()->route('orders.index')->withStatus(__('Order status succesfully changed.'));
    }

    public function rateOrder(Request $request, Order $order)
    {
        //dd($request->all());
        $restorant = $order->restorant;

        $rating = new Rating;
        $rating->rating = $request->ratingValue;
        $rating->user_id = auth()->user()->id;
        $rating->order_id = $order->id;
        $rating->comment = $request->comment;

        $restorant->ratings()->save($rating);

        $id_rating = $rating->id;

        $id_ask = $request->id_ask;
        $var = 'optionsRadios';
        for ($i = 0; $i < sizeof($id_ask); ++$i) {
            $obj = $id_ask[$i];
            $name = $var . $obj;
            // if (isset($request->$name)) {
            $register_encuesta = EncuestaClient::create([

                'id_question' => $id_ask[$i],
                'answer'  => $request->$name,
                'id_ratings'  => $id_rating,
            ]);
            // }
        }
        if ($register_encuesta->save()) {
            return redirect()->route('orders.show', ['order'=>$order])->withStatus(__('Order succesfully rated!'));
        } else {
            session()->flash('error', "Ha ocurrido un error al registrar la Encuesta");
            return redirect()->route('orders.show', ['order'=>$order]);
        }

        
    }

    public function checkOrderRating(Order $order)
    {
        $rating = DB::table('ratings')->select('rating')->where(['order_id' => $order->id])->get()->first();
        $is_rated = false;

        if (! empty($rating)) {
            $is_rated = true;
        }

        return response()->json(
            [
                'rating' => $rating->rating,
                'is_rated' => $is_rated,
                ]
        );
    }

    public function guestOrders()
    {
        $previousOrders = Cookie::get('orders') ? Cookie::get('orders') : '';
        $previousOrderArray = array_filter(explode(',', $previousOrders));

        //Find the orders
        $orders = Order::whereIn('id', $previousOrderArray)->orderBy('id', 'desc')->get();
        $backUrl = url()->previous();
        foreach ($orders as $key => $order) {
            $backUrl = route('vendor', $order->restorant->subdomain);
        }

        return view('orders.guestorders', ['backUrl'=>$backUrl, 'orders'=>$orders, 'statuses'=>Status::pluck('name', 'id')]);
    }


    public function generateOrderMsg($address, $comment, $price)
    {
        $title = 'New order #'.strtoupper(Str::random(5))."\n\n";

        $price = '*Price*: '.$price.' '.config('settings.cashier_currency')."\n\n";

        $items = '*Order:*'."\n";
        foreach (Cart::getContent() as $key => $item) {
            $items .= strval($item->quantity).' x '.$item->name."\n";
        }
        $items .= "\n";
        $final = $title.$price.$items;

        if ($address != null) {
            $final .= '*Address*:'."\n".$address."\n\n";
        }

        if ($comment != null) {
            $final .= '*Comment:*'."\n".$comment."\n\n";
        }

        return urlencode($final);
    }

    public function fbOrderMsg(Request $request)
    {
        $orderPrice = Cart::getSubTotal();

        $title = 'New order #'.strtoupper(Str::random(5))."\n\n";

        $price = '*Price*: '.$orderPrice.' '.config('settings.cashier_currency')."\n\n";

        $items = '*Order:*'."\n";
        foreach (Cart::getContent() as $key => $item) {
            $items .= strval($item->quantity).' x '.$item->name."\n";
        }
        $items .= "\n";
        $final = $title.$price.$items;

        if ($request->address != null) {
            $final .= '*Address*:'."\n".$request->address."\n\n";
        }

        if ($request->comment != null) {
            $final .= '*Comment:*'."\n".$request->comment."\n\n";
        }

        return response()->json(
            [
                'status' => true,
                'msg' => $final,
            ]
        );
    }

    public function storeWhatsappOrder(Request $request)
    {
        $restorant_id = null;
        foreach (Cart::getContent() as $key => $item) {
            $restorant_id = $item->attributes->restorant_id;
        }

        $restorant = Restorant::findOrFail($restorant_id);

        $orderPrice = Cart::getSubTotal();

        if ($request->exists('deliveryType')) {
            $isDelivery = $request->deliveryType == 'delivery';
        }

        $text = $this->generateWhatsappOrder($request->exists('addressID') ? $request->addressID : null, $request->exists('comment') ? $request->comment : null, $orderPrice);

        $url = 'https://wa.me/'.$restorant->whatsapp_phone.'?text='.$text;

        Cart::clear();

        return Redirect::to($url);
    }

    public function cancel(Request $request)
    {   
        $order = Order::findOrFail($request->order);
        return view('orders.cancel', ['order' => $order]);
    }
    
    public function  silentWhatsAppRedirect(Request $request){
        $order = Order::findOrFail($request->order);
        $message=$order->getSocialMessageAttribute(true);
        $url = 'https://api.whatsapp.com/send?phone='.$order->restorant->whatsapp_phone.'&text='.$message;
        return view('orders.success', ['order' => $order,'showWhatsApp'=>false,'whatsappurl'=>$url]);
    }

    public function success(Request $request)
    {   
        $order = Order::findOrFail($request->order);
        //If order is not paid - redirect to payment
        if($request->redirectToPayment.""=="1"&&$order->payment_status != 'paid'&&strlen($order->payment_link)>5){
            //Redirect to payment
            return redirect($order->payment_link);
        } 

        //If we have whatsapp send
        if($request->has('whatsapp')){
            $message=$order->getSocialMessageAttribute(true);
            $url = 'https://api.whatsapp.com/send?phone='.$order->restorant->whatsapp_phone.'&text='.$message;
            return Redirect::to($url);
        }

        //Should we show whatsapp send order
        $showWhatsApp=config('settings.whatsapp_ordering_enabled');

        if($showWhatsApp){
            //Disable when WhatsApp Mode
            if(config('settings.is_whatsapp_ordering_mode')){
                $showWhatsApp=false;
            }

            //In QR, if owner phone is not set, hide the button
            //In FT, we use owner phone to have the number
            if(strlen($order->restorant->whatsapp_phone)<3){
                $showWhatsApp=false;
            }
        }

        
        return view('orders.success', ['order' => $order,'showWhatsApp'=>$showWhatsApp]);
    }
    public function notificacion($index=1)
    {
        if($index==-1){
            $ee=auth()->user()->unreadNotifications()->update(['read_at' => now()]);
            return  json_encode(array("error"=>false));
        }else{
            $page=($index-1)*10;
            $notificacion=auth()->user()->notifications()->offset($page)->limit(10)->get();
            //$e=auth()->user()->unreadNotifications()->update(['read_at' => now()]);
            return json_encode(array("data"=>$notificacion,"total"=>auth()->user()->notifications()->count(),"totalNo"=>auth()->user()->notifications()->where("read_at",null)->count()));
        }
       
    }
}
