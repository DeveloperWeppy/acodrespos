<?php

namespace App\Http\Controllers;

use App\Models\EncuestaOrden;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EncuestaOrdenController extends Controller
{
    
    public function index()
    {
        $questions = EncuestaOrden::whereNull('deleted_at')->get();
        return view('encuesta.index', compact('questions'));
    }

    public function store(Request $request)
    {
        $error = false;
        $mensaje = '';

            $register_question = array(
                'question' => $request->question,
            );

            if (EncuestaOrden::create($register_question)) {
                $error = false;
                $mensaje = 'Registro de Pregunta Exitosa!';
            } else {
                $error = true;
                $mensaje = 'Error! Se presento un problema al registrar la pregunta, intenta de nuevo.';
            }
        echo json_encode(array('error' => $error, 'mensaje' => $mensaje));
    }

    public function update(Request $request, EncuestaOrden $interests)
    {
        $error = false;
        $mensaje = '';

            $update_interest = array(
                'question' => $request->question,
            );

            if (EncuestaOrden::findOrFail($request->id)->update($update_interest)) {
                $error = false;
                $mensaje = 'Interés Actualizado Exitosamente!';
            } else {
                $error = true;
                $mensaje = 'Error! Se presento un problema al actualizar los datos del interés, intenta de nuevo.';
            }
        echo json_encode(array('error' => $error, 'mensaje' => $mensaje));
    }

    public function destroy($id)
    {
        $error = false;
        $mensaje = '';

        $now = Carbon::now()->format('Y-m-d');
        //dd($now );
            $register_question = array(
                'deleted_at' => $now,
            );

            if (EncuestaOrden::findOrFail($id)->update($register_question)) {
                $error = false;
            } else {
                $error = true;
                $mensaje = 'Error! Se presento un problema al eliminar la pregunta, intenta de nuevo.';
            }
        echo json_encode(array('error' => $error, 'mensaje' => $mensaje));
    }
}
