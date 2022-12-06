<?php

namespace App\Http\Controllers;

use App\Tables;
use App\RestoArea;
use App\Restorant;
use App\User;
use Illuminate\Http\Request;
use App\Models\ConfigReservation;
use App\Models\ReservationReason;
use App\Models\ReservationConfig;
use App\Models\ReservationTables;
use App\Models\ConfigCuentasBancarias;
use App\Models\Reservation;
use App\Models\ReservationClientsController;
use DB;



class ConfigReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $restaurant_id = auth()->user()->restorant->id;
        
        #mesas get
        $restoareas = RestoArea::where('restaurant_id', $restaurant_id)->where('deleted_at', null)->get();
        $restomesas = Tables::where('restaurant_id', $restaurant_id)->where('deleted_at', null)->orderBy('restoarea_id')->get();

        $compani = Restorant::find($restaurant_id);
        $motivos = ReservationReason::where('companie_id', $restaurant_id)->get();
        
        return view('reservation.admin.index', compact('restoareas', 'restomesas', 'compani', 'motivos'));
    }

    public function geInfoMesas(Request $request)
    {
        $restaurant_id = auth()->user()->restorant->id;
        try {
            $zona = $request->input('zona');
            $tables = Tables::where('restoarea_id', $zona)->where('restaurant_id', $restaurant_id)->get();
            $response = ['data' => $tables];
        } catch (\Exception $exception) {
            return response()->json(['message' => 'There was an error retrieving the records'], 500);
        }
        return response()->json($response);
    }


    public function getInfoConfig(Request $request)
    {
        $restaurant_id = auth()->user()->restorant->id;

        $restaurantConfig = DB::table('reservations_config')
            ->select(DB::raw('reservations_config.*,(select group_concat(table_id) from reservation_tables where reservation_tables.companie_id=reservations_config.companie_id ) as mesas'))
            ->where('companie_id', $restaurant_id)->get();

        return response()->json($restaurantConfig);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner')) {

            $restaurant_id = auth()->user()->restorant->id;
            
            $clients = User::role('client')->where(['active'=>1])->get();

            $restoareas = RestoArea::where('restaurant_id', $restaurant_id)->where('deleted_at', null)->get();
            $restomesas = Tables::where('restaurant_id', $restaurant_id)->where('deleted_at', null)->orderBy('restoarea_id')->get();

            $motive = ReservationReason::where(['active'=>1])->get();

            $vendor=Restorant::findOrFail($restaurant_id);
            $configaccountsbanks = ConfigCuentasBancarias::where('rid',$vendor->id)->get();

            $restaurantConfig = DB::table('reservations_config')
            ->select(DB::raw('reservations_config.*,(select group_concat(table_id) from reservation_tables where reservation_tables.companie_id=reservations_config.companie_id ) as mesas'))
            ->where('companie_id', $restaurant_id)->get();

            return view('reservation.admin.includes.create', compact('clients','restoareas','restomesas','motive','configaccountsbanks','restaurantConfig'));

        } else {
            return redirect()->route('orders.index')->withStatus(__('No Access'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (auth()->user()->hasRole('owner')) {
            $restaurant_id = auth()->user()->restorant->id;

            $porc = 0;
            if(isset($request->porc)){
                $porc = $request->porc;
            }

            $restaurant = new Reservation;
            $restaurant->companie_id = $restaurant_id;
            $restaurant->client_id = $request->cli;
            $restaurant->check_percentage = $porc;
            $restaurant->reservation_reason_id = $request->mot;
            $restaurant->description = $request->com;
            $restaurant->payment_method = $request->met;
            $restaurant->payment_status = '';
            $restaurant->last_status = '';
            $restaurant->active = 1;
            $restaurant->note = '';
            $restaurant->observations ='';
            $restaurant->date = $request->fec." ".$request->hora.":00";
            $restaurant->total = $request->total;
            $restaurant->save();

            if ($request->hasFile('img_payment')) {
                
             
                $path = 'uploads/reservations/';
                $nom = $orderId.'.png';

                $order = Reservation::find($restaurant->id);
                $order->url_payment = $path.$nom;
                $order->id_account_bank = $request->cuentaid;
                $order->save();
    
                $request->img_payment->move(public_path($path), $nom);
            }
    
            if ($request->tipotarjeta) {
                $orderId=$request->orderid;
                $order=Order::findOrFail($orderId);
                $order->type_card = $request->tipotarjeta;
                $order->franquicia = $request->franquicia;
                $order->voucher = $request->voucher;
                $order->save();
    
                return $order->id;
                die();
            }

            if(isset($request->zonas)){
                ReservationClientsController::where('reservation_id','=',$restaurant->id)->delete();
                foreach($request->zonas as $key){
                    
                    $mesas = ReservationClientsController::updateOrCreate(
                        [
                            'reservation_id' => $restaurant->id,
                            'client_id' => $request->cli,
                            'table_id' => $key,
                        ],
                    );
                }
            }

            



        } else {
            return redirect()->route('orders.index')->withStatus(__('No Access'));
        }
    }

    public function storeConfig(Request $request)
    {
       
        
        $usersDriver = ReservationConfig::updateOrCreate(
            ['companie_id' => auth()->user()->restorant->id],
            [
            'minimum_period' => strip_tags($_POST['time_reservation']),
            'condition_period' => $_POST['time_reservation_number'],
            'percentage_payment' => strip_tags($_POST['porcentage_payment']),
            'wait_time' => strip_tags($_POST['wait_time']),
            'standard_price' => strip_tags($_POST['standard_price']),
            'check_no_cost' => (isset($_POST['check_no_cost'])?$_POST['check_no_cost']:0),
            ]
        );

        if(isset($request->zonas)){
            foreach($request->zonas as $key){
                $mesas = ReservationTables::updateOrCreate(
                    [
                        'companie_id' => auth()->user()->restorant->id,
                        'table_id' => $key,
                    ],
                    [
                        'price' => 0,
                    ]
                );
            }
        }

        echo 1;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ConfigReservation  $configReservation
     * @return \Illuminate\Http\Response
     */
    public function show(ConfigReservation $configReservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ConfigReservation  $configReservation
     * @return \Illuminate\Http\Response
     */
    public function edit(ConfigReservation $configReservation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ConfigReservation  $configReservation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConfigReservation $configReservation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ConfigReservation  $configReservation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConfigReservation $configReservation)
    {
        //
    }
}
