<?php

namespace App\Http\Controllers;

use App\Restorant;
use Illuminate\Http\Request;
use App\Models\ReservationReason;
use App\Http\Controllers\Controller;

class ReservationReasonController extends Controller
{
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
        $error = false;
        $mensaje = '';

        $numcuenta_validate =  ReservationReason::where('name', $request->name)->where('companie_id', $request->restaurant_id)->exists();

        if ($numcuenta_validate) {
            return redirect()->back()->withStatus(__('error','Este motivo de reservación ya se encuentra registrado!'));
        } else {
            $register = ReservationReason::create([
                'companie_id' => $request->restaurant_id, 
                'name' => $request->name, 
                'description' => $request->description, 
                'price' => $request->price
            ]);

            if ($register->save()) {
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
    public function destroy(ReservationReason $reservationReason)
    {
        //
    }
}
