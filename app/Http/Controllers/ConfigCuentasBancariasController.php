<?php

namespace App\Http\Controllers;

use App\Models\ConfigCuentasBancarias;
use Illuminate\Http\Request;

class ConfigCuentasBancariasController extends Controller
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
        $numcuenta_validate =  ConfigCuentasBancarias::where('number_account', $request->number_account)->where('rid', $request->rid)->exists();

        if ($numcuenta_validate) {
            return redirect()->back()->withStatus(__('error','Esta cuenta Bancaria ya existe'));
        } else {
            $register = ConfigCuentasBancarias::create([
                'rid' => $request->rid, 
                'name_receptor' => $request->name_receptor, 
                'name_bank' => $request->name_bank, 
                'type_document' => $request->type_document, 
                'number_document' => $request->number_document, 
                'type_account' => $request->type_account, 
                'number_account' => $request->number_account
            ]);

            if ($register->save()) {
                return redirect()->back()->withStatus(__('Cuenta de Banco agregada correctamente.'));
            } else {
                return redirect()->back()->withStatus(__('error','Ha ocurrido un error'));
            }
            
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ConfigCuentasBancarias  $configCuentasBancarias
     * @return \Illuminate\Http\Response
     */
    public function show(ConfigCuentasBancarias $configCuentasBancarias)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ConfigCuentasBancarias  $configCuentasBancarias
     * @return \Illuminate\Http\Response
     */
    public function edit(ConfigCuentasBancarias $configCuentasBancarias)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ConfigCuentasBancarias  $configCuentasBancarias
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConfigCuentasBancarias $configCuentasBancarias)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ConfigCuentasBancarias  $configCuentasBancarias
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConfigCuentasBancarias $configCuentasBancarias)
    {
        //
    }
}
