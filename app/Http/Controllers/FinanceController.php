<?php

namespace App\Http\Controllers;

use App\Exports\FinancesExport;
use App\Order;
use App\Restorant;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;
use App\Status;

use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    private function getResources()
    {
        $restorants = Restorant::where(['active'=>1])->get();
        $drivers = User::role('driver')->where(['active'=>1])->get();
        $clients = User::role('client')->where(['active'=>1])->get();

        $orders = Order::orderBy('created_at', 'desc');

        //Get client's orders
        if (auth()->user()->hasRole('client')) {
            $orders = $orders->where(['client_id'=>auth()->user()->id]);
        } elseif (auth()->user()->hasRole('driver')) {
            $orders = $orders->where(['driver_id'=>auth()->user()->id]);
        //Get owner's restorant orders
        } elseif (auth()->user()->hasRole('owner')) {
            $orders = $orders->where(['restorant_id'=>auth()->user()->restorant->id]);
        }

        //FILTER BT RESTORANT
        if (isset($_GET['restorant_id'])) {
            $orders = $orders->where(['restorant_id'=>$_GET['restorant_id']]);
        }
        //If restorant owner, get his restorant orders only
        if (auth()->user()->hasRole('owner')) {
            //Current restorant id
            $restorant_id = auth()->user()->restorant->id;
            $orders = $orders->where(['restorant_id'=>$restorant_id]);
        }

        //BY CLIENT
        if (isset($_GET['client_id'])) {
            $orders = $orders->where(['client_id'=>$_GET['client_id']]);
        }

        //BY DRIVER
        if (isset($_GET['driver_id'])) {
            $orders = $orders->where(['driver_id'=>$_GET['driver_id']]);
        }

        //BY DATE FROM
        if (isset($_GET['fromDate']) && strlen($_GET['fromDate']) > 3) {
            $orders = $orders->whereDate('created_at', '>=', $_GET['fromDate']);
        }

        //BY DATE TO
        if (isset($_GET['toDate']) && strlen($_GET['toDate']) > 3) {
            $orders = $orders->whereDate('created_at', '<=', $_GET['toDate']);
        }

        if (isset($_GET['payment_status'])) {
            $orders = $orders->where('payment_status',$_GET['payment_status']);
        }

        if (isset($_GET['status_id'])) {

            $orders->where(DB::raw('(select status_id from order_has_status where order_id=orders.id order by status_id desc limit 1)'),'=',$_GET['status_id']);
            /*
            $orders = $orders->whereHas('laststatus', function($q){
                $q->where('status_id', [$_GET['status_id']]);
            });
            */
        }

        return ['orders' => $orders, 'restorants'=>$restorants, 'drivers'=>$drivers, 'clients'=>$clients];
    }

    public function adminFinances()
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $resources = $this->getResources();
        $resources['orders'] = $resources['orders']->where('payment_status','paid')->whereNotNull('payment_method');

        //With downloaod
        if (isset($_GET['report'])) {
            $items = [];
            foreach ($resources['orders']->get() as $key => $order) {
                $item = [
                    'order_id'=>$order->id,
                    'restaurant_name'=>$order->restorant->name,
                    'restaurant_id'=>$order->restorant_id,
                    'created'=>$order->created_at,
                    'last_status'=>$order->status->pluck('alias')->last(),
                    'client_name'=>$order->client?$order->client->name:"",
                    'client_id'=>$order->client_id,
                    'address'=>$order->address ? $order->address->address : '',
                    'address_id'=>$order->address_id,
                    'driver_name'=>$order->driver ? $order->driver->name : '',
                    'driver_id'=>$order->driver_id,
                    'payment_method'=>$order->payment_method,
                    'srtipe_payment_id'=>$order->srtipe_payment_id,
                    'restaurant_fee'=>$order->fee,
                    'order_fee'=>$order->fee_value,
                    'restaurant_static_fee'=>$order->static_fee,
                    'platform_fee'=>$order->fee_value + $order->static_fee,
                    'processor_fee'=>$order->payment_processor_fee,
                    'delivery'=>$order->delivery_price,
                    'net_price_with_vat'=>$order->order_price_with_discount,
                    'discount'=>$order->discount,
                    'vat'=>$order->vatvalue,
                    'net_price'=>$order->order_price_with_discount - $order->vatvalue,
                    'order_total'=>$order->delivery_price + $order->order_price_with_discount,
                  ];
                array_push($items, $item);
            }

            return Excel::download(new FinancesExport($items), 'finances_'.time().'.xlsx');
        }

        //CARDS
        $cards = [
            ['title'=>'Orders', 'value'=>0],
            ['title'=>'Total', 'value'=>0, 'isMoney'=>true],
            // ['title'=>'Platform Fee', 'value'=>0, 'isMoney'=>true],
            ['title'=>'Net', 'value'=>0, 'isMoney'=>true],

            ['title'=>'Processor fee', 'value'=>0, 'isMoney'=>true],
            ['title'=>'Deliveries', 'value'=>0],
            ['title'=>'Delivery income', 'value'=>0, 'isMoney'=>true],
            ['title'=>'Platform profit', 'value'=>0, 'isMoney'=>true],
        ];
        foreach ($resources['orders']->get() as $key => $order) {
            $cards[0]['value'] += 1;
            $cards[1]['value'] += $order->delivery_price + $order->order_price_with_discount;
            // $cards[2]['value'] += $order->fee_value + $order->static_fee;
            $cards[2]['value'] += $order->order_price_with_discount - $order->fee_value - $order->static_fee;

            $cards[3]['value'] += $order->payment_processor_fee;
            $cards[4]['value'] += $order->delivery_method.'' == '1' ? 1 : 0;
            $cards[5]['value'] += $order->delivery_price;
            $cards[6]['value'] += $order->fee_value + $order->static_fee + $order->delivery_price - $order->payment_processor_fee;
        }

        $displayParam = [
            'cards'=> $cards,
            'orders' => $resources['orders']->paginate(10),
            'restorants'=>$resources['restorants'],
            'drivers'=>$resources['drivers'],
            'clients'=>$resources['clients'],
            'parameters'=>count($_GET) != 0,
            'statuses'=>Status::pluck('name','id')->toArray()
        ];

        return view('finances.index', $displayParam);
    }

    public function ownerFinances()
    {
        if (! auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action.');
        }

        //Find this owner restaurant
        $restaurant = auth()->user()->restorant;

        //Change currency
        \App\Services\ConfChanger::switchCurrency( $restaurant);

        //Check if Owner has completed
        $stripe_details_submitted = __('No');
        if (auth()->user()->stripe_account) {
            //Set our key
            Stripe::setApiKey(config('settings.stripe_secret'));

            $stripe_details_submitted = Account::retrieve(
                auth()->user()->stripe_account, []
              )->details_submitted ? __('Yes') : __('No');
        }

        $resources = $this->getResources();

        $resources['orders'] = $resources['orders']->whereNotNull('payment_method')->where('payment_status','paid');

        //With downloaod
        if (isset($_GET['report'])) {
            $items = [];
            $name_status ='';
            foreach ($resources['orders']->get() as $key => $order) {
                if($order->status->pluck('alias')->last() == 'delivered'){
                    $name_status ='Entregado';
                }else if($order->status->pluck('alias')->last()== 'just_created'){
                    $name_status ='Recien Creado';
                }else if($order->status->pluck('alias')->last()== 'accepted_by_admin'){
                    $name_status ='Aceptado por el Administrador';
                }else if($order->status->pluck('alias')->last()== 'accepted_by_restaurant'){
                    $name_status ='Aceptado por el Restaurante';
                }else if($order->status->pluck('alias')->last()== 'prepared'){
                    $name_status ='Preparado';
                }else if($order->status->pluck('alias')->last()== 'picked_up'){
                    $name_status ='Recogido';
                }else if($order->status->pluck('alias')->last()== 'rejected_by_restaurant'){
                    $name_status ='Rechazado por el restaurante';
                }else if($order->status->pluck('alias')->last()== 'rejected_by_admin'){
                    $name_status ='Rechazado por el Administrador';
                }else if($order->status->pluck('alias')->last()== 'updated'){
                    $name_status ='Actualizado';
                }else if($order->status->pluck('alias')->last()== 'closed'){
                    $name_status ='Cancelado';
                }
                $item = [
                    'N?? de Orden'=>$order->id,
                    'Nombre del Restaurante'=>$order->restorant->name,
                    //'restaurant_id'=>$order->restorant_id,
                    'Fecha de la Orden'=>$order->created_at,
                    '??ltimo estado del Pedido'=>$name_status,
                    'Nombre del Cliente'=>$order->client ? $order->client->name : '',
                    //'client_id'=>$order->client_id,
                    'Direcci??n'=>$order->address ? $order->address->address : '',
                    //'address_id'=>$order->address_id,
                    'Nombre del Domiciliario'=>$order->driver ? $order->driver->name : '',
                    //'driver_id'=>$order->driver_id,
                    'Metodo de Pago'=>$order->payment_method,
                    //'srtipe_payment_id'=>$order->srtipe_payment_id,
                    //'restaurant_fee'=>$order->fee,
                    //'order_fee'=>$order->fee_value,
                    //'restaurant_static_fee'=>$order->static_fee,
                    //'platform_fee'=>$order->fee_value + $order->static_fee,
                    //'processor_fee'=>$order->payment_processor_fee,
                    'Costo de Domicilio'=>$order->delivery_price,
                    'Valor Neto con Impoconsumo'=>$order->order_price_with_discount,
                    'Valor de Impoconsumo'=>$order->vatvalue,
                    'Valor Neto'=>$order->order_price_with_discount - $order->vatvalue,
                    'Total de la Orden'=>$order->delivery_price + $order->order_price_with_discount,
                    'discount'=>$order->discount
                  ];
                array_push($items, $item);
            }

            return Excel::download(new FinancesExport($items), 'finances_'.time().'.xlsx');
        }

        //CARDS
        $cards = [
            ['title'=>'Orders', 'value'=>0],
            ['title'=>'Total', 'value'=>0, 'isMoney'=>true],
            // ['title'=>'Platform Fee', 'value'=>0, 'isMoney'=>true],
            ['title'=>'Total Neto Con ICO', 'value'=>0, 'isMoney'=>true],

            ['title'=>'Impoconsumo (ICO)', 'value'=>0, 'isMoney'=>true],
            ['title'=>'Total Neto', 'value'=>0, 'isMoney'=>true],
            ['title'=>'Deliveries', 'value'=>0],
            ['title'=>'Costo Domicilio', 'value'=>0, 'isMoney'=>true],
        ];
        foreach ($resources['orders']->get() as $key => $order) {
            $cards[0]['value'] += 1;
            $cards[1]['value'] += $order->delivery_price + $order->order_price_with_discount;
            //$cards[2]['value'] += $order->fee_value + $order->static_fee;
            $cards[2]['value'] += $order->order_price_with_discount - $order->fee_value - $order->static_fee;

            $cards[3]['value'] += $order->vatvalue;
            $cards[4]['value'] += $order->order_price_with_discount - $order->vatvalue - $order->fee_value - $order->static_fee;
            $cards[5]['value'] += $order->delivery_method.'' == '1' ? 1 : 0;
            $cards[6]['value'] += $order->delivery_price;
        }

        
        //$estados = ['Accepted by admin','Accepted by restaurant','Assigned to driver','Closed','Delivered','Just created'];  
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
    

        $displayParam = [
            'cards'=> $cards,
            'orders' => $resources['orders']->paginate(10),
            'restorants'=>$resources['restorants'],
            'drivers'=>$resources['drivers'],
            'clients'=>$resources['clients'],
            'parameters'=>count($_GET) != 0,
            'stripe_details_submitted'=>$stripe_details_submitted,
            'showFeeTerms'=>true,
            'showStripeConnect'=>true,
            'restaurant'=>$restaurant,
            'weHaveStripeConnect'=>env('ENABLE_STRIPE_CONNECT', false),
            'statuses'=>$estados
        ];
        //Status::pluck('name','id')->toArray()
        return view('finances.index', $displayParam);
    }

    public function connect()
    {

        //Set our key
        Stripe::setApiKey(config('settings.stripe_secret'));

        if (! auth()->user()->stripe_account) {
            //Create account for client
            $account_id = Account::create([
                'type' => 'standard',
            ])->id;

            //Save this id in user object
            auth()->user()->stripe_account = $account_id;
            auth()->user()->update();
        } else {
            $account_id = auth()->user()->stripe_account;
        }

        //Set account
        $account_links = AccountLink::create([
            'account' => $account_id,
            'refresh_url' => route('finances.owner'),
            'return_url' => route('finances.owner'),
            'type' => 'account_onboarding',
            ]);

        return redirect()->away($account_links->url);
    }
}
