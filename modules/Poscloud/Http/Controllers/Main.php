<?php


namespace Modules\Poscloud\Http\Controllers;

use PDO;
use Cart;
use App\User;
use DateTime;
use App\Order;
use App\Tables;
use App\Restorant;
use Carbon\Carbon;
use Akaunting\Money\Money;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\SimpleDelivery;
use App\Models\CartStorageModel;
use App\Http\Controllers\Controller;
use Darryldecode\Cart\CartCollection;
use Akaunting\Module\Facade as Module;
use App\Models\ConfigCuentasBancarias;
use App\Repositories\Orders\OrderRepoGenerator;

class Main extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
    
        if(auth()->user()){
            if(auth()->user()->restaurant_id==null){
                $this->getRestaurant();
            }
            $vendor=Restorant::findOrFail(auth()->user()->restaurant_id);

            $canDoOrdering =$vendor->getPlanAttribute()['canMakeNewOrder'];
            if(!$canDoOrdering){
                return redirect()->route('orders.index')->withStatus(__('You can not receive more orders. Please subscribe to new plan.'));
            } 
                

            //Associative array for the floor plan
            $floorPlan=[];
            foreach ($vendor->areas as $key => $area) {
                foreach ($area->tables as $table) {
                    $floorPlan[$table->id]=$area->name." - ".$table->name;
                }
            }


            //Change currency
            \App\Services\ConfChanger::switchCurrency($vendor);

            //Create all the time slots
            $timeSlots = $this->getTimieSlots($vendor);


            $deliveryAreas=SimpleDelivery::where('restaurant_id',$vendor->id)->get();
            $deliveryAreasCost=SimpleDelivery::where('restaurant_id',$vendor->id)->pluck('cost','id')->toArray();
            $listClient=User::role('client')->where('active','1')->get();
            $selectClient=array();
            $selectTelefono=array();
            $clienteGeneral=User::role('client')->where('name','cliente general')->get();
            
            if(count($clienteGeneral)>0){
                $clienteGeneral=$clienteGeneral[0];
                array_push($selectClient,array('id'=>$clienteGeneral->id,'text'=>$clienteGeneral->name));
                $selectTelefono[$clienteGeneral->id]="";
            }else{
              $clienteGeneral= (object) array('id' => 0); 
              array_push($selectClient,array('id'=>'','text'=>"Seleccióna un cliente"));
            }
            foreach ($listClient as $key => $item) {
                    if($clienteGeneral->id!=$item->id){
                        array_push($selectClient,array('id'=>$item->id,'text'=>$item->name." - ".$item->number_identification));
                        $selectTelefono[$item->id]=$item->phone;
                    }
            }
            $configaccountsbanks = ConfigCuentasBancarias::where('rid',$vendor->id)->get();
            return view('poscloud::index',['configaccountsbanks'=>$configaccountsbanks,'deliveryAreasCost'=>$deliveryAreasCost,'deliveryAreas'=>$deliveryAreas,'timeSlots'=>$timeSlots,'vendor'=>$vendor,'restorant'=>$vendor,'floorPlan'=>$floorPlan,'selectClient'=>$selectClient,'selectTelefono'=>$selectTelefono]);
        }else{
            return redirect(route('login'));
        }
        
    }

    public function moveOrder($tableFrom,$tableTo){
        $order=CartStorageModel::where('vendor_id',auth()->user()->restaurant_id)->where('id',$tableFrom."_cart_items")->first();
        if($order){
            $order->id=$tableTo."_cart_items";
            $order->update();
            return response()->json([
                'status' => true,
                'message'=>__('Order moved successfully')
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message'=>__('Order on this table can not be found')
            ]);
        }
    }

    public function orders(){

        \App\Services\ConfChanger::switchCurrency(Restorant::where('id', auth()->user()->restaurant_id)->first());

        //Get all the active orders
        $orders=CartStorageModel::where('vendor_id',auth()->user()->restaurant_id)->get();

        //Create an array to suit our needs
        $returnArray=[];
        // $formatter = new \IntlDateFormatter(config('app.locale'), \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
        $formatter = new \IntlDateFormatter('en', \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT);
        $formatter->setPattern(config('settings.datetime_workinghours_display_format_new'));
        foreach ($orders as $key => $order) {

            $theOrder=new CartCollection($order->cart_data);
            $sum = $theOrder->sum(function ($item) {
                return $item->getPriceSum();
            });
            //dd($theOrder);
            $theTable=$order->type==3?Tables::findOrFail($order->id):null;
            if($sum!=0){
                array_push($returnArray,[
                    'id'=>$order->id,
                    'receipt_number'=>$order->receipt_number,
                    'employee'=>$order->user->name,
                    'date'=> $this->fechaCastellano($order->created_at),
                    'table'=>$order->type==3?$theTable->restoarea->name."-".$theTable->name:"",
                    'expedition'=>$order->type,
                    'type'=>$order->type==3?__('Dine in'):($order->type==2?__('Takeaway'):__('Delivery')),
                    'total'=> Money($sum, config('settings.cashier_currency'), config('settings.do_convertion'))->format(),
                    'config'=>$order->getAllConfigs()
                ]);
            }else{
                //When order value is 0 - and has no items - remove it
                $order->delete();
            }
            
        }

        return response()->json([
            'status' => true,
            'count' => count($returnArray),
            'orders'=>$returnArray
        ]);
    }

    function fechaCastellano ($fecha) {
        $fecha = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $dia = date('l', strtotime($fecha));
        $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
        $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $nombredia = str_replace($dias_EN, $dias_ES, $dia);
        $hora = Carbon::parse($fecha)->format('h:i:s');
        return $nombredia.": ".$hora;
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('poscloud::create');
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
        $order=[];
        if($request->order_id>0){
            $order = Order::findOrFail($request->order_id);
            $order=$order->items()->get();
        }
        foreach (Cart::getContent() as $key => $item) {
            $ifAdd=true;
            foreach ($order as $key2 => $item2) {
                if($item->attributes->id==$item2->id && $item->id==$item2->pivot->cart_item_id){
                    $ifAdd=false; 
                }
            }
            if($ifAdd){
                $extras=[];
                foreach ($item->attributes->extras as $keyExtra => $extra_id) {
                    array_push($extras,array('id'=>$extra_id));
                }
                array_push($items,array(
                    "id"=>$item->attributes->id,
                    "qty"=>$item->quantity,
                    "cart_item_id"=>$item->id,
                    "item_observacion"=>$item->observacion,
                    "variant"=>$item->attributes->variant,
                    "extrasSelected"=>$extras
                ));
            }
        }


        //stripe token
        $stripe_token=null;

        //Custom fields
        $customFields=[];
        if($request->has('custom')){
            $customFields=$request->custom;
        }

        //DELIVERY METHOD
        //Default - dinein - by default
        $delivery_method="dinein";
        if($request->has('expedition')){
            if($request->expedition==1){
                $delivery_method="delivery";
            }else if($request->expedition==2){
                $delivery_method="pickup";
            }
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


        $requestData=[
            'vendor_id'   => $vendor_id,
            'delivery_method'=> $delivery_method,
            'payment_method'=> $request->paymentType,
            'address_id'=>$request->addressID,
            "timeslot"=>$request->timeslot,
            "items"=>$items,
            "comment"=>$request->comment,
            "stripe_token"=>$stripe_token,
            "dinein_table_id"=>$table_id,
            "phone"=>$phone,
            "customFields"=>$customFields,
            "coupon_code"=>$request->has('coupon_code')?$request->coupon_code:null
        ];

        

        return new Request($requestData);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {

        //Guarda la imagen de la orden
        if ($request->hasFile('img_payment')) {
            $orderId=$request->orderid;
            $path = 'uploads/payments/';
            $nom = $orderId.'.png';
            $order=Order::findOrFail($orderId);
            $order->url_payment = $path.$nom;
            $order->id_account_bank = $request->cuentaid;
            $order->save();

            $request->img_payment->move(public_path($path), $nom);

            return $order->id;
            die();
        }

        if ($request->tipotarjeta) {
            $orderId=$request->orderid;
            $order=Order::findOrFail($orderId);
            $order->type_card = $request->tipotarjeta;
            $order->save();

            return $order->id;
            die();
        }
        
        if(auth()->user()){
            config(['shopping_cart.storage' => \App\Repositories\CartDBStorageRepository::class]); 
           
            $vendor=Restorant::findOrFail( auth()->user()->restaurant_id);
       
            if(isset($request->session_id)){
                Cart::session($request->session_id);
            }
            //Convert web request to mobile like request
            $mobileLikeRequest=$this->toMobileLike($request);
        

            //Data
            $vendor_id =  $mobileLikeRequest->vendor_id;
            $expedition= $mobileLikeRequest->delivery_method;
            $hasPayment= $request->paymentType=="onlinepayments";
            $isStripe= false;
            $vendorHasOwnPayment=null;

            

            $vendor=Restorant::findOrFail($mobileLikeRequest->vendor_id);

            //Payment methods
            foreach (Module::all() as $key => $module) {
                if($module->get('isPaymentModule')){
                    if($vendor->getConfig($module->get('alias')."_enable","false")=="true"){
                        $vendorHasOwnPayment='all';
                    }
                }
            }

            if($vendorHasOwnPayment==null){
                $hasPayment=false;
            }else{
                //Since v3, don't auto select payment model, show all the  options to  user
                $vendorHasOwnPayment="all";
            }

            //Repo Holder
            $orderRepo=OrderRepoGenerator::makeOrderRepo($vendor_id,$mobileLikeRequest,$expedition,$hasPayment,$isStripe,true, $vendorHasOwnPayment,"POS");

             //Proceed with validating the data
            $validator=$orderRepo->validateData();
            if ($validator->fails()) { 
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                ]);
            }

            //Proceed with making the order   POSOrderRepository
            if($request->has('custom')){
                $customFields=$request->custom;
                if(isset($customFields['client_id'])) {
                    $validatorOnMaking=$orderRepo->makeOrder($customFields['client_id'],$request->order_comment);
                }else{
                    $validatorOnMaking=$orderRepo->makeOrder();
                }
            }else{
                $validatorOnMaking=$orderRepo->makeOrder(null,$request->order_comment,$request->tipo,$request->order_id,$request->cart_id,$request->propina,$request->number_people);
            }
            if ($validatorOnMaking->fails()) { 
                return response()->json([
                    'status' => false,
                    'message' => $validatorOnMaking->errors()->first(),
                ]);
            }
            if(!isset($orderRepo->order->items)){
                $itemss=Order::findOrFail($orderRepo->order->id);
                $orderRepo->order->items=$itemss->items()->get();
            }
            
            return response()->json([
                'status' => true,
                'message' => __('Order finalized'),
                'order'=>$orderRepo->order,
                'id'=>$orderRepo->order->id,
                'paymentLink'=>$orderRepo->paymentRedirect
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => __("Signed out"),
            ]);
     
        }
         
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('poscloud::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('poscloud::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request)
    {
        //Find the table id 
        $cs=CartStorageModel::where('id',$request->table_id."_cart_items")->first();

        if(!$cs){
            return response()->json([
                'status' => false,
                'message' => __('Please add at least one item first'),
            ]);
        }

        //Set config
        $cs->setMultipleConfig($request->all());

        return response()->json([
            'status' => true,
            'message' => __('Order updated'),
            'datas'=>$cs->getAllConfigs()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
