<?php

namespace App\Http\Controllers;

use App\Restorant;
use App\Models\Log;
use App\Models\Reservation;
use Illuminate\Http\Request;

use App\Models\ReservationReason;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReservationReasonController extends Controller
{


    private function authChecker()
    {
        $this->ownerOnly();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $function = $this->getIpLocation();
        $error = false;
        $mensaje = '';
        $restaurant_id = auth()->user()->restorant->id;

        $numcuenta_validate =  ReservationReason::where('id', $request->motive_id)->where('companie_id', $restaurant_id)->exists();

        if ($numcuenta_validate) {
            $register = ReservationReason::where('id',$request->motive_id)->update([
                'name' => $request->name, 
                'description' => $request->description, 
                'price' => $request->price
            ]);

            if ($register) {
                Log::create([
                    'user_id' => Auth::user()->id,
                    'ip' => $request->ip(),
                    'module' => 'RESERVAS',
                    'submodule' => 'MOTIVO DE RESERVA',
                    'action' => 'Actualización',
                    'detail' => 'Se actualizó el motivo de reservación por parte del restaurante, ' .auth()->user()->restorant->name,
                    'country' => $function->country,
                    'city' =>$function->city,
                    'lat' =>$function->lat,
                    'lon' =>$function->lon,
                ]);
                $error = false;
                $mensaje = 'Modifcación de Motivo Exitosa';
            } else {
                $error = true;
                $mensaje = 'Error! Se presento un problema al modificar el motivo de reserva, intenta de nuevo.';
            }
        } else {
            $register = ReservationReason::create([
                'companie_id' => $restaurant_id, 
                'name' => $request->name, 
                'description' => $request->description, 
                'price' => $request->price
            ]);

            if ($register->save()) {
                Log::create([
                    'user_id' => Auth::user()->id,
                    'ip' => $request->ip(),
                    'module' => 'RESERVAS',
                    'submodule' => 'MOTIVO DE RESERVA',
                    'action' => 'Registro',
                    'detail' => 'Se registró un nuevo motivo de reservación por parte del restaurante, ' .auth()->user()->restorant->name,
                    'country' => $function->country,
                    'city' =>$function->city,
                    'lat' =>$function->lat,
                    'lon' =>$function->lon,
                ]);
                $error = false;
                $mensaje = 'Registro de Motivo Exitosa';
            } else {
                $error = true;
                $mensaje = 'Error! Se presento un problema al registrar el motivo de reserva, intenta de nuevo.';
            }
        }
        echo json_encode(array('error' => $error, 'mensaje' => $mensaje));
    }

    


    
    public function cargarMotivos()
    {
        $restaurant_id = auth()->user()->restorant->id;
        $motivos = ReservationReason::where('companie_id', $restaurant_id)->get();
        return view('reservation.admin.includes.cargarmotivos', compact('motivos'))->render();
    }

    public function getMotivos()
    {
        $idd = $_POST['motivo_id'];
        $motivo =  ReservationReason::where('id', $idd)->get();
        return response()->json($motivo);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ReservationReason  $reservationReason
     * @return \Illuminate\Http\Response
     */
    public function show(ReservationReason $reservationReason)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ReservationReason  $reservationReason
     * @return \Illuminate\Http\Response
     */
    public function edit(ReservationReason $reservationReason)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ReservationReason  $reservationReason
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReservationReason $reservationReason)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ReservationReason  $reservationReason
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $function = $this->getIpLocation();
        $this->authChecker();

        $restaurant_id = auth()->user()->restorant->id;


        $contador = Reservation::where('companie_id', $restaurant_id)->where('reservation_reason_id','=',$id)->count();

        if($contador==0){
            $item = ReservationReason::findOrFail($id);
            $item->delete();
            Log::create([
                'user_id' => Auth::user()->id,
                'ip' => $function->ip,
                'module' => 'RESERVAS',
                'submodule' => 'MOTIVO DE RESERVA',
                'action' => 'Eliminación',
                'detail' => 'Se eliminó un motivo de reservación por parte del restaurante, ' .auth()->user()->restorant->name,
                'country' => $function->country,
                'city' =>$function->city,
                'lat' =>$function->lat,
                'lon' =>$function->lon,
            ]);
            return redirect('/reservas')->with('success','El motivo ha sido removido');
        }
        
        return redirect('/reservas')->with('error','El motivo esta relacionado con algunas reservas');



        //return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_removed', ['item'=>__($this->title)]));
        
    }
}
