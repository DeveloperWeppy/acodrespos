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

use Carbon\Carbon;



class ConfigReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if(auth()->user()->hasRole('owner')){
            $restaurant_id = auth()->user()->restorant->id;

            $vendor=Restorant::findOrFail($restaurant_id);
            $configaccountsbanks = ConfigCuentasBancarias::where('rid',$vendor->id)->get();
            
            #mesas get
            $restoareas = RestoArea::where('restaurant_id', $restaurant_id)->where('deleted_at', null)->get();
            $restomesas = Tables::where('restaurant_id', $restaurant_id)->where('deleted_at', null)->orderBy('restoarea_id')->get();

            $compani = Restorant::find($restaurant_id);
            $motivos = ReservationReason::where('companie_id', $restaurant_id)->get();

            $restaurantConfig = DB::table('reservations_config')
                ->select(DB::raw('reservations_config.*,(select group_concat(table_id) from reservation_tables where reservation_tables.companie_id=reservations_config.companie_id ) as mesas'))
                ->where('companie_id', $restaurant_id)->get();

            $reservaciones = DB::table('reservations')->select(DB::raw('reservations.*,(select name from users where id=client_id) as cli,(select name from reservation_reasons where id=reservation_reason_id) as mot,client_id as tab'))->where('companie_id', $restaurant_id)->where('reservation_status','=','0')->orWhere('reservation_status','=','3')->orderBy('id','desc')->paginate(10);
            
            $solicitudes = DB::table('reservations')->select(DB::raw('reservations.*,(select name from users where id=client_id) as cli,(select name from reservation_reasons where id=reservation_reason_id) as mot,client_id as tab'))->where('companie_id', $restaurant_id)->where('reservation_status','!=','0')->orderBy('id','desc')->paginate(10);

            
            return view('reservation.admin.index', compact('restoareas', 'restomesas', 'compani', 'motivos','reservaciones','configaccountsbanks','restaurantConfig','solicitudes'));
        }

        if(auth()->user()->hasRole('client')){
    
            $reservaciones = DB::table('reservations')->select(DB::raw('reservations.*,(select name from reservation_reasons where id=reservation_reason_id) as mot'))->where('client_id', auth()->user()->id)->orderBy('id','desc')->paginate(10);
            
            return view('reservation.client.index', compact('reservaciones'));
            
        }
       
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

        if (auth()->user()->hasRole('owner')) {

            $restaurant_id = auth()->user()->restorant->id;

            $now =Carbon::now('America/Bogota')->format('Y-m-d');
            
            $clients = User::role('client')->where(['active'=>1])->get();

            $motive = ReservationReason::where('companie_id', $restaurant_id)->where(['active'=>1])->get();


            $areasMesas = [];
            $mesas = DB::table('reservation_tables')->select(DB::raw('group_concat(table_id) as idm'))->where('companie_id', $restaurant_id)->first();
            if($mesas->idm!=""){
                $idm = explode(',',$mesas->idm);
                $areas = DB::table('tables')->select(DB::raw('group_concat(restoarea_id) as ida'))->where('restaurant_id', $restaurant_id)->whereIn('id',$idm)->first();
                if($areas->ida!=""){
                    $ida = explode(',',$areas->ida);
                    $areas = DB::table('restoareas')->where('restaurant_id', $restaurant_id)->whereIn('id',$ida)->get();
                    $areasMesas = [];
                    for($i=0;$i<count($areas);$i++){
                        $areasMesas[$i][0] = $areas[$i];
                        $messa = DB::table('tables')->where('restaurant_id', $restaurant_id)->where('restoarea_id', $areas[$i]->id)->whereIn('id',$idm)->get();
                        $areasMesas[$i][1]=$messa;
                    }
                }
            }

            

            $vendor=Restorant::findOrFail($restaurant_id);
            $configaccountsbanks = ConfigCuentasBancarias::where('rid',$vendor->id)->get();

            $restaurantConfig = DB::table('reservations_config')
            ->select(DB::raw('reservations_config.*,(select group_concat(table_id) from reservation_tables where reservation_tables.companie_id=reservations_config.companie_id ) as mesas'))
            ->where('companie_id', $restaurant_id)->get();

            return view('reservation.admin.includes.create', compact('clients','areasMesas','motive','configaccountsbanks','restaurantConfig','now'));

        } 
        if (auth()->user()->hasRole('client')) {
            $restaurant=Restorant::where('active','=','1')->get();
            $now =Carbon::now('America/Bogota')->format('Y-m-d');

            return view('reservation.client.includes.create',compact('restaurant','now'));

        }
        return redirect()->route('orders.index')->withStatus(__('No Access'));
        
    }

    public function getHours(){
        $restaurant_id = auth()->user()->restorant->id;
        $jornada = DB::table('hours')->where('restorant_id', $restaurant_id)->get();
        return response()->json($jornada);
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
            $payment_status = "paid";
            if(isset($request->porc) && $request->porc==1){
                $porc = $request->porc;
                $payment_status = "pending";
            }

            $pago1 = [
                'metodo'=> $request->met,
                'cuenta_id'=> $request->cuentaid,
                'tarjeta'=> $request->tipotarjeta,
                'franquicia'=> $request->franquicia,
                'voucher'=> $request->voucher,
                'total'=> $request->pagado,
            ];

            //captura la hora y la convierte en formato de 24 horas
            list($time, $ampm) = explode(' ', $request->hora);
            list($hh, $mm) = explode(':', $time);
            if($ampm == 'AM' && $hh == 12) {
                $hh = '00';
            } elseif($ampm == 'PM' && $hh < 12) {
                $hh += 12;
            }
            if(!isset($request->com)){
                $request->com = "";
            }


            $reservation = new Reservation;
            $reservation->companie_id = $restaurant_id;
            $reservation->client_id = $request->cli;
            $reservation->check_percentage = $porc;
            $reservation->reservation_reason_id = $request->mot;
            $reservation->description = $request->com;
            $reservation->payment_status = $payment_status;
            $reservation->active = 1;
            $reservation->note = '';
            $reservation->observations ='';
            $reservation->date_reservation = $request->fec." ".$hh.":".$mm;
            $reservation->total = $request->total;
            $reservation->pendiente = $request->pendiente;
            $reservation->payment_1 = json_encode($pago1);
            $reservation->save();

            $iddRes = $reservation->id;

            if(isset($request->zonas)){
                ReservationClientsController::where('reservation_id','=',$iddRes)->delete();
                foreach($request->zonas as $key){
                    $mesas = ReservationClientsController::updateOrCreate(
                        [
                            'reservation_id' => $iddRes,
                            'client_id' => $request->cli,
                            'table_id' => $key,
                            'date_reservation'=>$request->fec." ".$hh.":".$mm,
                        ],
                    );
                }
            }

            if ($request->hasFile('img_payment')) {
                $path = 'uploads/reservations/';
                $nom = $iddRes.'.png';

                $request->img_payment->move(public_path($path), $nom);

                $reservation=Reservation::findOrFail($iddRes);
                $reservation->url_payment1 = $path.$nom;
                $reservation->save();

            }


        } 

        if (auth()->user()->hasRole('client')) {

            $error = false;
            $restaurant_id = $request->res;

            //captura la hora y la convierte en formato de 24 horas
            list($time, $ampm) = explode(' ', $request->hora);
            list($hh, $mm) = explode(':', $time);
            if($ampm == 'AM' && $hh == 12) {
                $hh = '00';
            } elseif($ampm == 'PM' && $hh < 12) {
                $hh += 12;
            }
            if(!isset($request->com)){
                $request->com = "";
            }

            $reservation = new Reservation;
            $reservation->companie_id = $restaurant_id;
            $reservation->client_id = auth()->user()->id;
            $reservation->reservation_reason_id = $request->mot;
            $reservation->description = $request->com;
            $reservation->payment_status = 'unpaid';
            $reservation->reservation_status = 1;
            $reservation->note = '';
            $reservation->mesas = $request->mes;
            $reservation->personas = $request->per;
            $reservation->observations ='';
            $reservation->date_reservation = $request->fec." ".$hh.":".$mm;
            $reservation->total = $request->total;
            $reservation->pendiente = '0';
            $reservation->save();

            $iddRes = $reservation->id;

            if(isset($request->zonas)){
                ReservationClientsController::where('reservation_id','=',$iddRes)->delete();
                foreach($request->zonas as $key){
                    $mesas = ReservationClientsController::updateOrCreate(
                        [
                            'reservation_id' => $iddRes,
                            'client_id' => auth()->user()->id,
                            'table_id' => $key,
                            'date_reservation'=>$request->fec." ".$hh.":".$mm,
                        ],
                    );
                }
            }
            $registros = 1;
            return response()->json(array('error' => $error, 'datos' => $registros)); 
           

        } 
    }

    public function storePendiente(Request $request)
    {
        if (auth()->user()->hasRole('owner')) {
            $restaurant_id = auth()->user()->restorant->id;

            $pago2 = [
                'metodo'=> $request->met,
                'cuenta_id'=> $request->cuentaid,
                'tarjeta'=> $request->tipotarjeta,
                'franquicia'=> $request->franquicia,
                'voucher'=> $request->voucher,
                'total'=> $request->pagado,
            ];

            $reservation =Reservation::findOrFail($request->reserva_id);
            if(isset($request->solicitud)){

                list($time, $ampm) = explode(' ', $request->hora);
                list($hh, $mm) = explode(':', $time);

                if($ampm == 'AM' && $hh == 12) {
                    $hhto = '02';
                } elseif($ampm == 'PM' && $hh < 12) {
                    $hh += 12;
                }
                $hhto = $hh+2;  //le suma 2 horaas a la hora elegida para comprobar si la mesa esta ocupada la siguiente hora
                if($hh==23 || $hh===24){
                    $hhto = $hh;
                }

                $reservation =Reservation::findOrFail($request->reserva_id);
                $reservation->payment_status = 'paid';
                $reservation->date_reservation = $request->fec." ".$hh.":".$mm;
                $reservation->total = $request->total;
                $reservation->pendiente = $request->pendiente;
                $reservation->payment_1 = json_encode($pago2);
                $reservation->reservation_status = 3;
                $reservation->active = 1;

                if(isset($request->zonas)){
                    ReservationClientsController::where('reservation_id','=',$request->reserva_id)->delete();
                    foreach($request->zonas as $key){
                        $mesas = ReservationClientsController::updateOrCreate(
                            [
                                'reservation_id' => $request->reserva_id,
                                'client_id' => $request->cli,
                                'table_id' => $key,
                                'date_reservation'=>$request->fec." ".$hh.":".$mm,
                            ],
                        );
                    }
                }


            }else{
                $reservation =Reservation::findOrFail($request->reserva_id);
                $reservation->pendiente = 0;
                $reservation->payment_2= json_encode($pago2);
                $reservation->payment_status = 'paid';
            }
        
            $reservation->save();

            $iddRes = $request->reserva_id;

            if(isset($request->solicitud)){
                if ($request->hasFile('img_payment')) {
                    $path = 'uploads/reservations/';
                    $nom = $iddRes.'.png';

                    $request->img_payment->move(public_path($path), $nom);

                    $reservation=Reservation::findOrFail($iddRes);
                    $reservation->url_payment1 = $path.$nom;
                    $reservation->save();
                }
            }else{
                if ($request->hasFile('img_payment')) {
                    $path = 'uploads/reservations/';
                    $nom = $iddRes.'_2.png';

                    $request->img_payment->move(public_path($path), $nom);

                    $reservation=Reservation::findOrFail($iddRes);
                    $reservation->url_payment2 = $path.$nom;
                    $reservation->save();
                }
            }

           

            echo 1;


        } else {
            return redirect()->route('orders.index')->withStatus(__('No Access'));
        }
    }


    public function storeUpdate(Request $request)
    {
        if (auth()->user()->hasRole('owner')) {
            $restaurant_id = auth()->user()->restorant->id;

        
            $pago3 = [
                'metodo'=> $request->met,
                'cuenta_id'=> $request->cuentaid,
                'tarjeta'=> $request->tipotarjeta,
                'franquicia'=> $request->franquicia,
                'voucher'=> $request->voucher,
                'total'=> $request->pagado,
            ];

            $reservation =Reservation::findOrFail($request->reserva_id);
            $reservation->payment_3= json_encode($pago3);
            $reservation->save();

            $iddRes = $request->reserva_id;

            if ($request->hasFile('img_payment')) {
                $path = 'uploads/reservations/';
                $nom = $iddRes.'_3.png';

                $request->img_payment->move(public_path($path), $nom);

                $reservation=Reservation::findOrFail($iddRes);
                $reservation->url_payment3 = $path.$nom;
                $reservation->save();
            }

            echo 1;


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
            'anticipation_time' => strip_tags($_POST['anticipation_time']),
            'standard_price' => strip_tags($_POST['standard_price']),
            'update_price' => strip_tags($_POST['update_price']),
            'check_no_cost' => (isset($_POST['check_no_cost'])?$_POST['check_no_cost']:0),
            'interval_time' => (isset($_POST['interval'])?$_POST['interval']:0),
            ]
        );
     
        if(isset($request->zonas)){

            ReservationTables::where('companie_id','=',auth()->user()->restorant->id)->delete();
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


    public function inactiveReservation(Request $request){
        $reservacion = Reservation::find($request->reserva_id);
        $reservacion->active = 0;
        $reservacion->save();
    }

    public function getOcupation(Request $request){
        
        $error = false;

        if(isset($request->fecha,$request->hora,$request->mesas)){
            list($time, $ampm) = explode(' ', $request->hora);
            list($hh, $mm) = explode(':', $time);

            if($ampm == 'AM' && $hh == 12) {
                $hhto = '02';
            } elseif($ampm == 'PM' && $hh < 12) {
                $hh += 12;
            }
            $hhto = $hh+2;  //le suma 2 horaas a la hora elegida para comprobar si la mesa esta ocupada la siguiente hora
            if($hh==23 || $hh===24){
                $hhto = $hh;
            }
            
            $fecha = $request->fecha." ".$hh.":".$mm;
            $fechato = $request->fecha." ".$hhto.":".$mm;

            $registros = 0;

            if(isset($request->reserva_id)){
                $restaurant_id = $request->restaurant_id;
                $reservation=DB::table('reservations')->where('companie_id','=',$restaurant_id)->where('date_reservation','=',$fecha)->first();
                if(isset($reservation) && $reservation->id!=$request->reserva_id){
                    $registros = 1;
                }
            }else{
                $restaurant_id = auth()->user()->restorant->id;
                $reservation=DB::table('reservations')->select(DB::raw('group_concat(id) as ids'))->where('companie_id','=',$restaurant_id)->where('date_reservation','=',$fecha)->first();
                if(isset($reservation) && $reservation->ids!=null && isset($request->mesas)){
                    $ids = explode(",",$reservation->ids);
                    $mesas = $request->mesas;
                    $mesas=DB::table('reservations_clients')->select(DB::raw('count(id) contador'))->whereIn('reservation_id',$ids)->whereIn('table_id',$mesas)->get();
    
                    $registros = $mesas[0]->contador;
                }
            }
        }

        
        
        //contar las tablas que estan dentro de ese id y dentro de las mesas que envio.
        return response()->json(array('error' => $error, 'datos' => $registros)); 
        
    }

    

    public function getTables(Request $request)
    {
        $reservation=DB::table('reservations_clients')->select(DB::raw('group_concat(table_id) as idr'))->where('reservation_id','=',$request->reservacion_id)->get();
          
        $mesas = [];
        if(isset($reservation) && $reservation[0]->idr!=null){
            $idm = explode(",",$reservation[0]->idr);
            $mesas = DB::table('tables')->select(DB::raw('tables.*,(select name from restoareas where id=restoarea_id) as area'))->whereIn('id',$idm)->get();
        }
        return view('reservation.admin.includes.tablamodalmesas', compact('mesas'))->render();
 
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
    public function edit($id)
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner')) {

            $restaurant_id = auth()->user()->restorant->id;

            $now =Carbon::now('America/Bogota')->format('Y-m-d');
            
            $clients = User::role('client')->where(['active'=>1])->get();

            $restoareas = RestoArea::where('restaurant_id', $restaurant_id)->where('deleted_at', null)->get();
            $restomesas = Tables::where('restaurant_id', $restaurant_id)->where('deleted_at', null)->orderBy('restoarea_id')->get();

            $motive = ReservationReason::where('companie_id', $restaurant_id)->where(['active'=>1])->get();

            $vendor=Restorant::findOrFail($restaurant_id);
            $configaccountsbanks = ConfigCuentasBancarias::where('rid',$vendor->id)->get();

            $restaurantConfig = DB::table('reservations_config')
            ->select(DB::raw('reservations_config.*,(select group_concat(table_id) from reservation_tables where reservation_tables.companie_id=reservations_config.companie_id ) as mesas'))
            ->where('companie_id', $restaurant_id)->get();

            
            $reservation=DB::table('reservations')->select(DB::raw('reservations.*,(select group_concat(table_id) from reservations_clients where reservation_id=reservations.id ) as mess'))->where('id','=',$id)->first();


            return view('reservation.admin.includes.edit', compact('clients','restoareas','restomesas','motive','configaccountsbanks','restaurantConfig','reservation','now'));

        } else {
            return redirect()->route('orders.index')->withStatus(__('No Access'));
        }
    }

    public function editsolicitud($id)
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner')) {

            $restaurant_id = auth()->user()->restorant->id;

            $now =Carbon::now('America/Bogota')->format('Y-m-d');
            
            $clients = User::role('client')->where(['active'=>1])->get();

            $motive = ReservationReason::where('companie_id', $restaurant_id)->where(['active'=>1])->get();

            $vendor=Restorant::findOrFail($restaurant_id);
            $configaccountsbanks = ConfigCuentasBancarias::where('rid',$vendor->id)->get();

            $restaurantConfig = DB::table('reservations_config')
            ->select(DB::raw('reservations_config.*,(select group_concat(table_id) from reservation_tables where reservation_tables.companie_id=reservations_config.companie_id ) as mesas'))
            ->where('companie_id', $restaurant_id)->get();

            
            $reservation=DB::table('reservations')->select(DB::raw('reservations.*,(select group_concat(table_id) from reservations_clients where reservation_id=reservations.id ) as mess'))->where('id','=',$id)->first();

            $areasMesas = [];
            $mesas = DB::table('reservation_tables')->select(DB::raw('group_concat(table_id) as idm'))->where('companie_id', $restaurant_id)->first();
            if($mesas->idm!=""){
                $idm = explode(',',$mesas->idm);
                $areas = DB::table('tables')->select(DB::raw('group_concat(restoarea_id) as ida'))->where('restaurant_id', $restaurant_id)->whereIn('id',$idm)->first();
                if($areas->ida!=""){
                    $ida = explode(',',$areas->ida);
                    $areas = DB::table('restoareas')->where('restaurant_id', $restaurant_id)->whereIn('id',$ida)->get();
                    $areasMesas = [];
                    for($i=0;$i<count($areas);$i++){
                        $areasMesas[$i][0] = $areas[$i];
                        $messa = DB::table('tables')->where('restaurant_id', $restaurant_id)->where('restoarea_id', $areas[$i]->id)->whereIn('id',$idm)->get();
                        $areasMesas[$i][1]=$messa;
                    }
                }
            }



            return view('reservation.admin.includes.editsolicitud', compact('clients','areasMesas','motive','configaccountsbanks','restaurantConfig','reservation','now'));

        } else {
            return redirect()->route('orders.index')->withStatus(__('No Access'));
        }
    }

    public function configRestaurant(Request $request){

        $error = false;
        $restaurant_id = $request->restaurant_id;
        $registros=[];

        $config = ReservationConfig::where('companie_id', $restaurant_id)->first();
        
        $areasMesas = [];
        $mesas = DB::table('reservation_tables')->select(DB::raw('group_concat(table_id) as idm'))->where('companie_id', $restaurant_id)->first();
        if($mesas->idm!=""){
            $idm = explode(',',$mesas->idm);
            $areas = DB::table('tables')->select(DB::raw('group_concat(restoarea_id) as ida'))->where('restaurant_id', $restaurant_id)->whereIn('id',$idm)->first();
            if($areas->ida!=""){
                $ida = explode(',',$areas->ida);
                $areas = DB::table('restoareas')->where('restaurant_id', $restaurant_id)->whereIn('id',$ida)->get();
                $areasMesas = [];
                for($i=0;$i<count($areas);$i++){
                    $areasMesas[$i][0] = $areas[$i];
                    $messa = DB::table('tables')->where('restaurant_id', $restaurant_id)->where('restoarea_id', $areas[$i]->id)->whereIn('id',$idm)->get();
                    $areasMesas[$i][1]=$messa;
                }
            }
        }

        $motive = ReservationReason::where('companie_id', $restaurant_id)->where(['active'=>1])->get();

        $jornadas = DB::table('hours')->where('restorant_id', $restaurant_id)->get();

        $registros[0]=$areasMesas;
        $registros[1]=$motive;
        $registros[2]=$jornadas;
        $registros[3]=$config;
        return response()->json(array('error' => $error, 'datos' => $registros)); 

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
