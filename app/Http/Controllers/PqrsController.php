<?php

namespace App\Http\Controllers;

use App\Order;
use App\Models\Pqrs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SolicitudPqrNotification;

class PqrsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user==null) {
            return view('pqrs.client.index');
        } else {
            
            $my_orders = Order::where('client_id', $user->id)->latest()
            ->take(5)->get();
            //dd($my_orders);
            return view('pqrs.client.index', compact('my_orders'));
        }
        
    }

    public function index_admin(Request $request)
    {
        $get_loc = geoip()->getLocation($request->ip());
        //dd($get_loc);
        $pqrs_all = Pqrs::paginate(7);
        return view('pqrs.admin.index', compact('pqrs_all'));
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

            $register_pqr = array(
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
            );
            # validamos si existe la imagen en el request
            if ($request->file('evidence')) {
                $evidence = $request->file('evidence')->store('public/evidence_pqrs');
                $url = Storage::url($evidence);

                $register_pqr['evidence'] = $url;
            }
            if (isset($request->type_radicate)) {
                $register_pqr['type_radicate'] = $request->type_radicate;
            } else {
                $register_pqr['type_radicate'] = "Solicitud Relacionada con un Pedido";
            }
            if (isset($request->n_order) && $request->n_order!='otro') {
                $register_pqr['order_id'] = $request->n_order;
            }
            if (isset($request->num_order)) {
                $register_pqr['num_order'] = $request->num_order;
            }
            
            if (Pqrs::create($register_pqr)) {
                $error = false;
                $mensaje = 'Registro de Pregunta Exitosa!';
            } else {
                $error = true;
                $mensaje = 'Error! Se presento un problema al registrar la pregunta, intenta de nuevo.';
            }
        echo json_encode(array('error' => $error, 'mensaje' => $mensaje));
    }

    public function storeRespuesta(Request $request)
    {
        $error = false;
        $mensaje = '';

            $update_pqr = array(
                'answer_radicate' => $request->message,
                'status' => 'Solicitud Respondida'
            );
            # validamos si existe la imagen en el request
            if ($request->file('evidence_answer')) {
                $evidence = $request->file('evidence_answer')->store('public/evidence_answer_pqrs');
                $url = Storage::url($evidence);

                $update_pqr['evidence_answer'] = $url;
            }
            
            if (Pqrs::findOrFail($request->id)->update($update_pqr)) {
                $radicat_pqr = Pqrs::find($request->id);
                $not = new SolicitudPqrNotification($radicat_pqr);
                $radicat_pqr->notify($not);

                $error = false;
                $mensaje = 'Registro de Pregunta Exitosa!';
            } else {
                $error = true;
                $mensaje = 'Error! Se presento un problema al registrar la pregunta, intenta de nuevo.';
            }
        echo json_encode(array('error' => $error, 'mensaje' => $mensaje));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pqrs  $pqrs
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $pqr =   Pqrs::find($id);
        return view('pqrs.admin.show', compact('pqr'));
    }

    public function detalle($id)
    {
        //session('id_pqr', $id);
        $pqr = Pqrs::find($id);
        return view('pqrs.show_public', compact('pqr', 'id'));
    }

    public function detalle_radicada($id)
    {
        
        if (Session::has('succeful_validateaccesspqr')) {
            $id_flash = Session::get('succeful_validateaccesspqr');

            if ($id==$id_flash) {
                Session::forget($id_flash);
                $pqr = Pqrs::find($id);
                return view('pqrs.show_public', compact('pqr'));
            } else {
                return redirect()->route('pqrs.validateaccespqr');
            }
        } else {
            return redirect()->route('pqrs.validateaccespqr');
        }
        
        
    }

    public function updateStatus(Request $request)
    {
        $error = false;
        $mensaje = '';

        if (isset($_POST['idpqr']) && !empty($_POST['idpqr'])) {
            $id_pqr = $request->idpqr;
            //dd($id_pqr);
            $update = array(
                'status' => 'en revision'
            );
            if ($pqr = Pqrs::findOrFail($id_pqr)->update($update)) {
                $radicat_pqr = Pqrs::find($id_pqr);
                $not = new SolicitudPqrNotification($radicat_pqr);
                $radicat_pqr->notify($not);
                //$radicat_pqr->notify(new SolicitudPqrNotification('En Revisión', $radicat_pqr->name, $radicat_pqr->type_radicate));
                $error = false;
                $mensaje = '¡Cambio de estado Exitoso!';
            } else {
                $error = true;
                $mensaje = 'Hubo un error al procesar la solicitud!';
            }
        }
        echo json_encode(array('error' => $error, 'mensaje' => $mensaje));
    }

    public function validate_acces(Request $request)
    {

        $email_user = $request->email;

        $pqr = Pqrs::where('email', $email_user)->exists();
            if ($pqr) {
                $pqr_info = Pqrs::where('email', $email_user)->first();
                $id_pqr = $pqr_info->id;
                session()->flash('succeful_validateaccesspqr', $id_pqr);
                    return redirect()->route('pqrs.detalle_radicada', $id_pqr);
            }else{
                return redirect()->back()->with('error', 'El Correo ingresado no es el mismo con el que se radicó la solicitud');
            }

    }
}
