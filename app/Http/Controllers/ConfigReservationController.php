<?php

namespace App\Http\Controllers;

use App\Tables;
use App\RestoArea;
use App\Restorant;
use Illuminate\Http\Request;
use App\Models\ConfigReservation;
use App\Models\ReservationReason;

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
        $zona ='seleccione';
        #mesas get
        $restoareas = RestoArea::where('restaurant_id', $restaurant_id)->where('deleted_at', null)->get();
        $compani = Restorant::find($restaurant_id);
        $motivos = ReservationReason::where('companie_id', $restaurant_id)->get();
        return view('reservation.admin.index', compact('restoareas', 'zona', 'compani', 'motivos'));
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
