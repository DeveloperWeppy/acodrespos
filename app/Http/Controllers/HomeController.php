<?php

namespace App\Http\Controllers;

use App\Items;
use App\Categories;
use App\Order;
use App\Restorant;
use App\User;
use Carbon\Carbon;
use DB;
use Spatie\Permission\Models\Role;
use Akaunting\Module\Facade as Module;
use App\Models\RestaurantClient;
use Modules\Expenses\Models\Expenses;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Stripe\OrderItem;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimeOrderExport;
use App\Exports\HourOrderExport;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    private  function driverInfo(){
            $driver = auth()->user();

             //Today paid orders
            $today=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::today());
        
            //Week paid orders
            $week=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::now()->startOfWeek());

            //This month paid orders
            $month=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=', Carbon::now()->startOfMonth());

            //Previous month paid orders 
            $previousmonth=Order::where(['driver_id'=>$driver->id])->where('payment_status','paid')->where('created_at', '>=',  Carbon::now()->subMonth(1)->startOfMonth())->where('created_at', '<',  Carbon::now()->subMonth(1)->endOfMonth());


            //This user driver_percent_from_deliver
            $driver_percent_from_deliver=intval(auth()->user()->getConfig('driver_percent_from_deliver',config('settings.driver_percent_from_deliver')))/100;

            $earnings = [
                'today'=>[
                    'orders'=>$today->count(),
                    'earning'=>$today->sum('delivery_price')*$driver_percent_from_deliver,
                    'icon'=>'bg-gradient-red'
                ],
                'week'=>[
                    'orders'=>$week->count(),
                    'earning'=>$week->sum('delivery_price')*$driver_percent_from_deliver,
                    'icon'=>'bg-gradient-orange'
                ],
                'month'=>[
                    'orders'=>$month->count(),
                    'earning'=>$month->sum('delivery_price')*$driver_percent_from_deliver,
                    'icon'=>'bg-gradient-green'
                ],
                'previous'=>[
                    'orders'=>$previousmonth->count(),
                    'earning'=>$previousmonth->sum('delivery_price')*$driver_percent_from_deliver,
                    'icon'=>'bg-gradient-info'
                ]
            ];

            return view('dashboard', [
                'earnings' => $earnings
            ]);
    }

    public function pureSaaSIndex($lang=null){
        $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
        if ($lang!=null) {
            //this is language route
            $locale = $lang;
        }
        if($locale!="android-chrome-256x256.png"){
            App::setLocale(strtolower($locale));
            session(['applocale_change' => strtolower($locale)]);
        }

        $dataToDisplay=[];
        $response = new \Illuminate\Http\Response(view('dashboard_pure', $dataToDisplay));
        $response->withCookie(cookie('lang', $locale, 120));
        App::setLocale(strtolower($locale));

        return $response;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index($lang=null)
    {

        if (config('settings.makePureSaaS',false)) {
            return $this->pureSaaSIndex($lang);
        }

        $locale = Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale');
        if ($lang!=null) {
            //this is language route
            $locale = $lang;  
        }

        if($locale!="android-chrome-256x256.png"){
            App::setLocale(strtolower($locale));
            session(['applocale_change' => strtolower($locale)]);
        }
       
        if (auth()->user()->hasRole('owner')||auth()->user()->hasRole('staff')) {
            \App\Services\ConfChanger::switchCurrency(auth()->user()->restorant);
        }
        
        $last30days=Carbon::now()->subDays(30);

        //Driver
        if (auth()->user()->hasRole('driver')) {
            return $this->driverInfo();            
        }elseif (auth()->user()->hasRole('client')) {
            return redirect()->route('front');
        }else if (auth()->user()->hasRole('admin')&&config('app.isft')){
            //Admin in FT
            $last30daysDeliveryFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('delivery_price');
            $last30daysStaticFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('static_fee');
            $last30daysDynamicFee = Order::all()->where('created_at', '>', $last30days)->where('payment_status','paid')->sum('fee_value');
            $last30daysTotalFee = DB::table('orders')
                                ->select(DB::raw('SUM(delivery_price + static_fee + fee_value) AS sumValue'))
                                ->where('created_at', '>', $last30days)
                                ->where('payment_status','paid')
                                ->value('sumValue');
        }else{
            $last30daysDeliveryFee = 0;
            $last30daysStaticFee = 0;
            $last30daysDynamicFee = 0;
            $last30daysTotalFee = 0;
        }



        //--- grafico de mesa caliente

        $tablesLabels=[];
        $tablesPeoples=[];
        $misMesas = [];
        $mesaMasCaliente = [];

        $ordenespordiaLabels = [];
        $ordenespordiaValues = [];

        

        if (auth()->user()->hasRole('owner')) {

            $misMesas = DB::table('restoareas')
            ->where('restaurant_id', auth()->user()->restorant->id)
            ->get();

            $are = $misMesas[0]->id;


            //FILTER BY area
            if(isset($_GET['tarea'])){
                $are = $_GET['tarea'];
            }

            //consulta todas las mesas por area
            $mesas = DB::table('orders')
            ->select('tables.restoarea_id','tables.name',DB::raw('count(orders.table_id) as numt'),DB::raw('sum(orders.number_people) as nump'))
            ->join('tables', 'tables.id', '=', 'orders.table_id')
            ->where('tables.restoarea_id',$are)
            ->where('orders.restorant_id', auth()->user()->restorant->id)
            ->groupBy('tables.restoarea_id','orders.table_id');
            

            //FILTER BY initial date
            if(isset($_GET['tinicio']) && $_GET['tinicio']!="" && $_GET['tfin']==""){
                $ini = $_GET['tinicio'];
                $mesas->whereDate('orders.created_at',"=","$ini");
            }
            //FILTER BY end date
            $fin = date('Y-m-d');
            if(isset($_GET['tinicio'],$_GET['tfin']) && $_GET['tinicio']!="" && $_GET['tfin']!=""){
                $ini = $_GET['tinicio'];
                $fin = $_GET['tfin'];
                $mesas->whereDate('orders.created_at',">=","$ini")->whereDate('orders.created_at',"<=","$fin");
            }
            
            //consulta la mesa mas ocupada 
            $mesaMasCaliente = DB::table('orders')
            ->select('tables.restoarea_id','tables.name as nomt',DB::raw('count(orders.table_id) as numt'),DB::raw('sum(orders.number_people) as nump'))
            ->join('tables', 'tables.id', '=', 'orders.table_id')
            ->where('orders.restorant_id', auth()->user()->restorant->id)
            ->where('tables.restoarea_id',$are)
            ->groupBy('tables.restoarea_id','orders.table_id')
            ->orderBy('nump','desc');

            //FILTER BY end date
            $fin = date('Y-m-d');
            if(isset($_GET['tinicio'],$_GET['tfin']) && $_GET['tinicio']!="" && $_GET['tfin']!=""){
                $ini = $_GET['tinicio'];
                $fin = $_GET['tfin'];
                $mesaMasCaliente->whereDate('orders.created_at',">=","$ini")->whereDate('orders.created_at',"<=","$fin")->first()->get();
            }

            
            foreach ($mesas->get() as $key => $mesa) {
                array_push($tablesLabels,$mesa->name);
                array_push($tablesPeoples,$mesa->nump);
            }



        }


        $periodLabels=[];
        $periodTime=[];
        if (auth()->user()->hasRole('owner')) {
            //excel tiempos por pedido
            $orders = Order::orderBy('delivery_method', 'desc')->whereNotNull('restorant_id');
            $orders = $orders->where(['restorant_id'=>auth()->user()->restorant->id]);
            $orders = $orders->whereHas('laststatus', function($q){
                $q->where('status_id', [7]);
            });

            $fin = date('Y-m-d');
            if(isset($_GET['pinicio'],$_GET['pfin']) && $_GET['pinicio']!="" && $_GET['pfin']!=""){
                $ini = $_GET['pinicio'];
                $fin = $_GET['pfin'];
                $orders->whereDate('created_at',">=","$ini")->whereDate('created_at',"<=","$fin");
            }

            
            $nomT = 0;
            $timT = 0;
            $k=-1;
            $numP = 0;

            foreach ($orders->get() as $key => $orde) {
                
                $to_time = strtotime($orde->updated_at);
                $from_time = strtotime($orde->created_at);
                $diff =  round(abs($to_time - $from_time) / 60,2);
                $timT=$timT+$diff;
                
                if($nomT!=$orde->delivery_method){
                    $numP=1;
                    $prom = $timT/$numP;
                    $nomT=$orde->delivery_method;
                    array_push($periodLabels,$orde->getExpeditionType());
                    array_push($periodTime,$prom);
                    $k++;
                    $timT=$diff;
                    
                }else{
                    $numP++;
                    $prom = $timT/$numP;
                    $periodTime[$k]=$prom;
                    
                }

            }
            
            if (isset($_GET['report'])) {
                $items = [];

                foreach ($orders->get() as $key => $order) {

                    $to_time = strtotime($order->updated_at);
                    $from_time = strtotime($order->created_at);
                    $diff=  round(abs($to_time - $from_time) / 60,2). " minute";

                    $item = [
                        'order_id'=>$order->id,
                        'last_status'=>$order->status->pluck('alias')->last(),
                        'client_name'=>$order->client ? $order->client->name : '',
                        'method'=>$order->getExpeditionType(),
                        'date-initial'=>$order->created_at,
                        'date-end'=>$order->updated_at,
                        'time'=>$diff
                      ];
                    array_push($items, $item);
                }
    
                return Excel::download(new TimeOrderExport($items), 'timeOrders_'.time().'.xlsx');
            }

        }

        $horarioLabels=['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
        $horarioOrders=[0,0,0,0,0,0,0];
        $meseros = [];
        if (auth()->user()->hasRole('owner')) {

            //graficos ventas por horario
            $ordenesHorario = Order::select('*',DB::raw('DAYOFWEEK(created_at) as dia'),DB::raw('count(id) as numo'),DB::raw('hour(created_at) as hor'))->where(['restorant_id'=>auth()->user()->restorant->id])->groupBy('dia')->orderBy('dia','asc');
            $ordenesHorario = $ordenesHorario->whereHas('laststatus', function($q){
                $q->where('status_id', [7]);
            });

            //FILTER BY end date
            if(isset($_GET['hinicio']) && $_GET['hinicio']!="" && $_GET['hfin']==""){
                $ini = $_GET['hinicio'];
                $ordenesHorario->whereDate('created_at',"=","$ini");
            }
            $fin = date('Y-m-d');
            if(isset($_GET['hinicio'],$_GET['hfin']) && $_GET['hinicio']!="" && $_GET['hfin']!=""){
                $ini = $_GET['hinicio'];
                $fin = $_GET['hfin'];
                $ordenesHorario->whereDate('created_at',">=","$ini")->whereDate('created_at',"<=","$fin");
            }
            //FILTER BY hour
            if(isset($_GET['hhde'],$_GET['hhha']) && $_GET['hhde']!="" && $_GET['hhha']!=""){
                $hini = $_GET['hhde'];
                $hfin = $_GET['hhha'];
                $ordenesHorario->where(DB::raw('hour(created_at)'),">=","$hini")->where(DB::raw('hour(created_at)'),"<=","$hfin");
            }

            
            foreach ($ordenesHorario->get() as $key => $hora) {
                $k=$hora->dia-2;
                $horarioOrders[$k]=$hora->numo;
            }
        

            if (isset($_GET['reportweekofday'])) {

                $ordenesHorarior = Order::where(['restorant_id'=>auth()->user()->restorant->id])->orderBy('created_at','asc');
                $ordenesHorarior = $ordenesHorarior->whereHas('laststatus', function($q){
                    $q->where('status_id', [7]);
                });


                if(isset($_GET['hinicio']) && $_GET['hinicio']!="" && $_GET['hfin']==""){
                    $ini = $_GET['hinicio'];
                    $ordenesHorarior->whereDate('created_at',"=","$ini");
                }
                //FILTER BY end date
                $fin = date('Y-m-d');
                if(isset($_GET['hinicio'],$_GET['hfin']) && $_GET['hinicio']!="" && $_GET['hfin']!=""){
                    $ini = $_GET['hinicio'];
                    $fin = $_GET['hfin'];
                    $ordenesHorarior->whereDate('created_at',">=","$ini")->whereDate('created_at',"<=","$fin");
                }
                //FILTER BY hour
                if(isset($_GET['hhde'],$_GET['hhha']) && $_GET['hhde']!="" && $_GET['hhha']!=""){
                    $hini = $_GET['hhde'];
                    $hfin = $_GET['hhha'];
                    $ordenesHorarior->where(DB::raw('hour(created_at)'),">=","$hini")->where(DB::raw('hour(created_at)'),"<=","$hfin");
                }

                $items = [];

                foreach ($ordenesHorarior->get() as $key => $order) {

                    $to_time = strtotime($order->updated_at);
                    $from_time = strtotime($order->created_at);
                    $diff=  round(abs($to_time - $from_time) / 60,2). " minute";

                    $item = [
                        'order_id'=>$order->id,
                        'last_status'=>$order->status->pluck('alias')->last(),
                        'client_name'=>$order->client ? $order->client->name : '',
                        'method'=>$order->getExpeditionType(),
                        'date-initial'=>$order->created_at,
                        'date-end'=>$order->updated_at,
                        'time'=>$diff
                      ];
                    array_push($items, $item);
                }
    
                return Excel::download(new HourOrderExport($items), 'hourOrders_'.time().'.xlsx');
            }
            

        }

        $ordenestotalpordiaLabels=[];
        $ordenestotalpordiaValues=[];

        if (auth()->user()->hasRole('owner')) {
            //excel ventas por dia
            $ordenestotalpordia=Order::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as dia,sum(order_price) as total'))
            ->where('restorant_id',auth()->user()->restorant->id)
            ->where('created_at', '>', $last30days)
            ->where('payment_status', 'paid')
            ->groupBy('dia')
            ->orderBy('dia');

            
            foreach ($ordenestotalpordia->get() as $key => $orden) {
                array_push($ordenestotalpordiaLabels,$orden->dia);
                array_push($ordenestotalpordiaValues,$orden->total);
            }
        }
        
        

        $doWeHaveExpensesApp=false; // Be default for other don't enable expenses

        if (auth()->user()->hasRole('staff')) {
            if(in_array("poscloud", config('global.modules',[]))){
                //Redirect to POS
                return redirect()->route('poscloud.index');
            }else{
                 //Redirect to Orders
                return redirect()->route('orders.index');
            }
           
        }
        if (auth()->user()->hasRole('manager')) {
            return redirect()->route('admin.restaurants.index');
        }
        if(isset($_GET['page'])){

        }
        else  if (! config('app.ordering')) {
            if (auth()->user()->hasRole('owner')) {
                return redirect()->route('admin.restaurants.edit', auth()->user()->restorant->id);
            } elseif (auth()->user()->hasRole('admin')) {
                return redirect()->route('admin.restaurants.index');
            }
        }

        $expenses=[
            'costValue'=>[]
        ];


        //grafico ventas por 
        $last30daysOrders = 0;
        $last30daysClientsRestaurant=[];
        $last30daysOrdersValue=[];
        $salesValue=[];
        $monthList=[];


        $last30daysOrdersValue = Order::where('created_at', '>', $last30days)
        ->where('payment_status','paid')
        ->select(DB::raw('ROUND(SUM(order_price+delivery_price),2) as order_price'),DB::raw('SUM(delivery_price + static_fee + fee_value) AS total_fee'),DB::raw('SUM(delivery_price) AS total_delivery'),DB::raw('SUM(static_fee) AS total_static_fee'),DB::raw('SUM(fee_value) AS total_fee_value'))
        ->first()->toArray();

        $last30daysClientsRestaurant = RestaurantClient::where('created_at', '>', $last30days)->count();

        if(auth()->user()->hasRole('owner')){
       
        $months = [
            1 => __('Jan'),
            2 => __('Feb'),
            3 => __('Mar'),
            4 => __('Apr'),
            5 => __('May'),
            6 => __('Jun'),
            7 => __('Jul'),
            8 => __('Aug'),
            9 => __('Sep'),
            10 => __('Oct'),
            11 => __('Nov'),
            12 => __('Dec'),
        ];
        
        $last30daysOrders = Order::where('created_at', '>', $last30days)->count();
        $last30daysOrdersValue = Order::where('created_at', '>', $last30days)
        ->where('payment_status','paid')
        ->select(DB::raw('ROUND(SUM(order_price+delivery_price),2) as order_price'),DB::raw('SUM(delivery_price + static_fee + fee_value) AS total_fee'),DB::raw('SUM(delivery_price) AS total_delivery'),DB::raw('SUM(static_fee) AS total_static_fee'),DB::raw('SUM(fee_value) AS total_fee_value'))
        ->first()->toArray();

        $sevenMonthsDate = Carbon::now()->subMonths(6)->startOfMonth();
        $salesValueRaw=Order::where('created_at', '>', $sevenMonthsDate)
                ->where('payment_status','paid')
                ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
                ->orderBy(DB::raw('YEAR(created_at), MONTH(created_at)'), 'asc')
                ->select(DB::raw('count(id) as totalPerMonth'),DB::raw('ROUND(SUM(order_price + delivery_price),2) AS sumValue'),DB::raw('MONTH(created_at) month'))
                ->get()->toArray();
        $monthsIds = array_map(function($o) { return $o['month'];}, $salesValueRaw);
        $salesValue = array_combine($monthsIds, $salesValueRaw);
        foreach ($salesValue as $key => &$sale) {
            $sale['monthName']=$months[$key];
        }

        $monthList=[];
        foreach ($salesValue as $key => $salerecord) {
           array_push($monthList,$salerecord['monthName']);
        }

    }

        //Expenses  - Owner only
        if (auth()->user()->hasRole('owner')&&Module::has('expenses')) {
            $doWeHaveExpensesApp=true;
            $last30daysCostValue = Expenses::where([['created_at', '>', $last30days]])->sum('amount');

            $expensesValueRaw=Expenses::where('created_at', '>', $sevenMonthsDate)
            ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
            ->orderBy(DB::raw('YEAR(date), MONTH(date)'), 'asc')
            ->select(DB::raw('SUM(amount) AS costValue'),DB::raw('MONTH(date) month'))
            ->get()->toArray();

            $monthsIds = array_map(function($o) { return $o['month'];}, $expensesValueRaw);
            $costValue = array_combine($monthsIds, $expensesValueRaw);
            foreach ($costValue as $monthKey => $cost) {
                if(isset($salesValue[$monthKey])){
                    $salesValue[$monthKey]['costValue']=$cost['costValue'];
                }
            }
            

            //Cost per group
            $last30daysCostPerGroup = Expenses::where([['created_at', '>', $last30days]])
                ->groupBy('expenses_category_id')
                ->select('id','expenses_category_id',DB::raw('SUM(amount) AS amount'))->get();
            $last30daysCostPerGroupLabels=[];
            $last30daysCostPerGroupValues=[];
            foreach ($last30daysCostPerGroup as $key => $category) {
                array_push($last30daysCostPerGroupLabels,$category->category->name);
                array_push($last30daysCostPerGroupValues,$category->amount);
            }
        
            //Cost per vedor
            $last30daysCostPerVendor = Expenses::where([['created_at', '>', $last30days]])
            ->groupBy('expenses_vendor_id')
            ->select('id','expenses_vendor_id',DB::raw('SUM(amount) AS amount'))->get();

            $last30daysCostPerVendorLabels=[];
            $last30daysCostPerVendorValues=[];
            foreach ($last30daysCostPerVendor as $key => $vendor) {
                array_push($last30daysCostPerVendorLabels,$vendor->vendor->name);
                array_push($last30daysCostPerVendorValues,$vendor->amount);
            }

            $expenses=[
                'last30daysCostValue'=>$last30daysCostValue,
                'costValue'=>$costValue,
                'last30daysCostPerGroupLabels'=>$last30daysCostPerGroupLabels,
                'last30daysCostPerGroupValues'=>$last30daysCostPerGroupValues,
                'last30daysCostPerVendorLabels'=>$last30daysCostPerVendorLabels,
                'last30daysCostPerVendorValues'=>$last30daysCostPerVendorValues,
            ];   
        }

      
        $availableLanguagesENV = config('settings.front_languages');
        $exploded = explode(',', $availableLanguagesENV);
        $availableLanguages = [];
        for ($i = 0; $i < count($exploded); $i += 2) {
            $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
        }

        $countItems=0;
        if(auth()->user()->hasRole('admin')){
            $countItems=Restorant::count();
        }
        if(auth()->user()->hasRole('owner')){
            if(auth()->user()->restorant&&auth()->user()->restorant->categories){
                $countItems=Items::whereIn('category_id', auth()->user()->restorant->categories->pluck('id')->toArray())->whereNull('deleted_at')->count();
            }
            $data=[];
            $datadias=[];
            $last7days = date("Y-m-d", strtotime(Carbon::now('America/Bogota')->format('Y-m-d') . "- 7 days"));
            $now=Carbon::now()->format('Y-m-d');
            //---------------------------los 10 productos más vendidos de los últimos 30 días del restaurante logueado

            $orders30days=DB::table('order_has_items')
                ->select(DB::raw('count(order_has_items.item_id) as cantidad, order_has_items.item_id as id_product'))
                ->join('orders', function ($join) use ($last30days){
                    $join->on('order_has_items.order_id','=','orders.id')
                        ->where('orders.created_at', '>', $last30days)
                        ->where('orders.restorant_id', auth()->user()->restorant->id);
                })
                ->groupBy('order_has_items.item_id')
                ->orderBy('cantidad', 'desc')
                ->limit(10);


            if(isset($_GET['fmes']) && $_GET['fmes']!=0){

                $year =  date("Y");
                $orders30days=DB::table('order_has_items')
                ->select(DB::raw('count(order_has_items.item_id) as cantidad, order_has_items.item_id as id_product'))
                ->join('orders', function ($join) use ($year){
                    $join->on('order_has_items.order_id','=','orders.id')
                    ->where(DB::raw('month(orders.created_at)'), '=', $_GET['fmes'])
                    ->where(DB::raw('year(orders.created_at)'), '=', "$year")
                    ->where('orders.restorant_id', auth()->user()->restorant->id);

                })
                ->groupBy('order_has_items.item_id')
                ->orderBy('cantidad', 'desc')
                ->limit(10);
            }

            if(isset($_GET['fcat']) && $_GET['fcat']==2){
                $orders30days=DB::table('order_has_items')
                ->select(DB::raw('count(order_has_items.item_id) as cantidad, order_has_items.item_id as id_product,items.category_id as catt'))
                ->join('orders', function ($join) use ($last30days){
                    $join->on('order_has_items.order_id','=','orders.id')
                        ->where('orders.created_at', '>', $last30days)
                        ->where('orders.restorant_id', auth()->user()->restorant->id);
                })
                ->join('items','order_has_items.item_id','=','items.id')
                ->groupBy('items.category_id')
                ->orderBy('category_id', 'desc')
                ->limit(10);


                if(isset($_GET['fmes']) && $_GET['fmes']!=0){
                    $year = date("Y");
                    $orders30days=DB::table('order_has_items')
                    ->select(DB::raw('count(order_has_items.item_id) as cantidad, order_has_items.item_id as id_product,items.category_id as catt'))
                    ->join('orders', function ($join) use ($year){
                        $join->on('order_has_items.order_id','=','orders.id')
                        ->where(DB::raw('month(orders.created_at)'), '=', $_GET['fmes'])
                        ->where(DB::raw('year(orders.created_at)'), '=', "$year")
                        ->where('orders.restorant_id', auth()->user()->restorant->id);
                    })
                    ->join('items','order_has_items.item_id','=','items.id')
                    ->groupBy('items.category_id')
                    ->orderBy('cantidad', 'desc')
                    ->limit(10);
                }
            }
            
        

            //recorrer las ordenes
            foreach ($orders30days->get() as $key => $value) {
                $id_product = $value->id_product;
                $cantidad= $value->cantidad;
                $item = Items::find($id_product);
                $name_product = $item->name;

                
                if(isset($_GET['fcat']) && $_GET['fcat']==2){
                    $id_category = $value->catt;
                    $cat = Categories::find($id_category);
                    $name_product = $cat->name;
                }
                $array = array(
                    'name_product'=>$name_product,
                    'cantidad'=>$cantidad
                );

                $data[] = array(
                    'datos'=>$array,
                );
            }

        
            
            //grafico de ventas por dia
            $meseros=User::role('staff')
            ->where(['active'=>1])
            ->where('restaurant_id',auth()->user()->restorant->id)->get();


            $ordenesprodia=Order::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as dia,COUNT(id) as cantidad'))
            ->where('delivery_method','3')
            ->where('restorant_id',auth()->user()->restorant->id)
            ->where('created_at', '>', $last30days)
            ->where('payment_status', 'paid')
            ->groupBy('dia')
            ->orderBy('dia');

            //filter by mesero
            if(isset($_GET['mmes']) && $_GET['mmes']!=0){
                $ordenesprodia = $ordenesprodia->where('employee_id', $_GET['mmes']);
            }
            //filter by fecha inicial
            if(isset($_GET['minicio']) && $_GET['minicio']!=""){
                $ini = $_GET['minicio'];
                $fin = $_GET['mfin'];
                $ordenesprodia->whereDate('created_at',">=","$ini")->whereDate('created_at',"<=","$fin");
            }

            $ordenespordiaLabels=[];
            $ordenespordiaValues=[];
            foreach ($ordenesprodia->get() as $key => $orden) {
                array_push($ordenespordiaLabels,$orden->dia);
                array_push($ordenespordiaValues,$orden->cantidad);
            }

            
        

            //-------------------------------------total de ventas de los 7 días de la semana
            $lastorders7days = DB::table('orders')
            ->select(DB::raw("created_at as fecha, DAYOFWEEK(created_at) as dias, sum(order_price) as total_orden"))
            ->whereBetween('created_at', [$last7days . ' 00:00:00', $now . ' 23:59:59'])
            ->where('restorant_id', auth()->user()->restorant->id)
            ->groupBy('dias')
            ->orderBy('dias', 'asc')
            ->get(); 

            if (!empty($lastorders7days)) {
                foreach ($lastorders7days as $key => $value) {
                    $day = $value->dias;
                    $total_orden = $value->total_orden;
                    $nombre_dia ='';
                    switch ($day) {
                        case 1:
                            $nombre_dia = 'Domingo';
                            break;
                        case 2:
                            $nombre_dia = 'Lunes';
                            break;
                        case 3:
                            $nombre_dia = 'Martes';
                            break;
                        case 4:
                            $nombre_dia = 'Miércoles';
                            break;
                        case 5:
                            $nombre_dia = 'Jueves';
                            break;
                        case 6:
                            $nombre_dia = 'Viernes';
                            break;
                        case 7:
                            $nombre_dia = 'Sábado';
                            break;
                    }
                    $datos = array(
                        'day'=>$nombre_dia,
                        'total_ordenes'=>$total_orden,
                    );
                    $datadias[] = array(
                        'datos'=>$datos,
                    );

                }
                $expenses['laste7days']=$datadias;
            }
            
            $expenses['data']=$data;
        }
        //dd($expenses);
        $dataToDisplay=[
            'availableLanguages'=>$availableLanguages,
            'locale'=>$locale,
            'expenses'=>$expenses,
            'doWeHaveExpensesApp'=>$doWeHaveExpensesApp,
            'last30daysOrders' => $last30daysOrders,
            'last30daysClientsRestaurant' => $last30daysClientsRestaurant,
            'last30daysOrdersValue'=> $last30daysOrdersValue,
            'allViews' => auth()->user()->hasRole('owner')?auth()->user()->restorant->views:Restorant::sum('views'),
            'salesValue' => $salesValue,
            'monthLabels' =>  $monthList,
            'countItems'=>$countItems,
            'last30daysDeliveryFee' =>  $last30daysDeliveryFee,
            'last30daysStaticFee' =>  $last30daysStaticFee,
            'last30daysDynamicFee' =>  $last30daysDynamicFee,
            'last30daysTotalFee' =>  $last30daysTotalFee,
            'tablesLabels' => $tablesLabels,
            'tablesPeoples' =>  $tablesPeoples,
            'misMesas'=>$misMesas,
            'mesaMasCaliente'=>$mesaMasCaliente,
            'periodLabels' => $periodLabels,
            'periodTime' =>  $periodTime,
            'horarioLabels' => $horarioLabels,
            'horarioOrders' =>  $horarioOrders,
            'parameters'=>count($_GET) != 0,
            'misMeseros'=>$meseros,
            'ordenespordiaLabels' => $ordenespordiaLabels,
            'ordenespordiaValues' =>  $ordenespordiaValues,
            'ordenestotalpordiaLabels' => $ordenestotalpordiaLabels,
            'ordenestotalpordiaValues' =>  $ordenestotalpordiaValues,
        ];
        
        $response = new \Illuminate\Http\Response(view('dashboard', $dataToDisplay));
        $response->withCookie(cookie('lang', $locale, 120));
        App::setLocale(strtolower($locale));

        return $response;
    }
}
