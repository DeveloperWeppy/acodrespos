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

            return view('reservation.admin.includes.create', compact('clients','restoareas','restomesas','motive','configaccountsbanks'));

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
