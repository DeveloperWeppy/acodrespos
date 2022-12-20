<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Order;
use App\Models\Log;
use App\Models\Pqrs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Notifications\SolicitudPqrNotification;
use Carbon\Carbon;
use ParagonIE\Sodium\Compat;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PqrsExport;

use App\Notifications\OrderNotification;

use App\Notifications\General;



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
        $now = Carbon::now('America/Bogota')->format('Y-m-d H:m:s');
        $pqrs_all = Pqrs::where('created_at', '<=', $now);


        if(isset($_GET['consecutive']) && $_GET['consecutive']!=""){
            $pqrs_all = $pqrs_all->where('consecutive_case',$_GET['consecutive']);
        }

        if(isset($_GET['email']) && $_GET['email']!=""){
            $pqrs_all = $pqrs_all->where('email',$_GET['email']);
        }

        if(isset($_GET['category']) && $_GET['category']!=""){
            $pqrs_all = $pqrs_all->where('type_radicate',$_GET['category']);
        }

        if(isset($_GET['estado']) && $_GET['estado']!=""){
            $pqrs_all = $pqrs_all->where('status',$_GET['estado']);
        }


        if (isset($_GET['report'])) {
            $items=[];
            $k=1;
            $pqrs_all=$pqrs_all->orderBy(DB::raw('FIELD(status,  "en revision", "radicado", "soluccionado")'),'desc');
            foreach ($pqrs_all->get() as $key => $item) {

                $hora = date_format($item->created_at, 'h:i A');
                $item = [
                    'fecha'=>date_format($item->created_at, 'Y-m-d')." - ".$hora,
                    'numero'=>$item->consecutive_case,
                    'usuario'=>$item->name,
                    'email'=>$item->email,
                    'tipo'=>$item->type_radicate,
                    'estado'=>$item->status,
                  ];
                array_push($items, $item);

                $k++;
            }

            return Excel::download(new PqrsExport($items), 'listadoPqrs_'.time().'.xlsx');
        }


        $pqrs_all=$pqrs_all->orderBy(DB::raw('FIELD(status,  "en revision", "radicado", "soluccionado")'),'desc')->paginate(7);

        $allPqrs = Pqrs::where('created_at', '<=', $now);

        $consecutives = $allPqrs->groupBy('consecutive_case')->get();
        $emails = $allPqrs->groupBy('email')->get();
        $category = $allPqrs->groupBy('type_radicate')->get();


        return view('pqrs.admin.index', compact('pqrs_all','consecutives','emails','category'));
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
        $url_confirm = '';
        $year_actual = date('Y');
        
        $register_pqr = array(
            'consecutive_case' => 'Nn',
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone1,
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
        
        if ($pq = Pqrs::create($register_pqr)) {
            $id_case = $pq->id;
            $consecutive_case = 'CAIM'.$id_case.'_'.$year_actual;
            $url_confirm = route('pqrs.confirmacion',$consecutive_case);

            $update_case = array(
                'consecutive_case' => $consecutive_case
            );
            Pqrs::findOrFail($id_case)->update($update_case);
            
            $error = false;
            $mensaje = 'Registro de Solicitud Exitosa!';

             //Notification
            $itemNotification = Pqrs::find($id_case);
            $userNotification = User::findOrFail('1');
            $userNotification->notify(new General($itemNotification, '1','Nueva solicitud PQRS','/pqrs/detalle-pqr','1'));
            
        } else {
            $error = true;
            $mensaje = 'Error! Se presento un problema al registrar la pregunta, intenta de nuevo.';
        }

       

        
        


        echo json_encode(array('error' => $error, 'mensaje' => $mensaje, 'case_id' => $url_confirm));
    }

    public function confirmacion($consecutive_case)
    {
        $get_pqr = Pqrs::where('consecutive_case', $consecutive_case)->first();
        return view('pqrs.client.confirmsolicitud', compact('get_pqr'));
    }
    public function storeRespuesta(Request $request)
    {
        $function = $this->getIpLocation();
        $error = false;
        $mensaje = '';

            $update_pqr = array(
                'answer_radicate' => $request->message,
                'status' => 'Solucionado'
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

                Log::create([
                    'user_id' => Auth::user()->id,
                    'ip' => $request->ip(),
                    'module' => 'PQR',
                    'submodule' => '',
                    'action' => 'Actualización',
                    'detail' => 'Se dió respuesta a la solicitud con id #'.$request->id,
                    'country' => $function->country,
                    'city' =>$function->city,
                    'lat' =>$function->lat,
                    'lon' =>$function->lon,
                ]);

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

    public function detalle($consecutive_case)
    {
        //session('id_pqr', $id);
        $pqr = Pqrs::where('consecutive_case',$consecutive_case)->first();
        return view('pqrs.show_public', compact('pqr', 'id'));
    }

    public function detalle_radicada($consecutive_case)
    {
        //dd($consecutive_case);
        if ($consecutive_case) {
            $pqr = Pqrs::where('consecutive_case',$consecutive_case)->first();
            return view('pqrs.show_public', compact('pqr'));
        } else {
            //session()->flash('consecutive_case', $consecutive_case);
            return redirect()->route('pqrs.validateaccespqr', $consecutive_case);
        }
        
        
    }

    public function updateStatus(Request $request)
    {
        $function = $this->getIpLocation();
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
                
                Log::create([
                    'user_id' => Auth::user()->id,
                    'ip' => $request->ip(),
                    'module' => 'PQR',
                    'submodule' => '',
                    'action' => 'Actualización',
                    'detail' => 'Se inició la revisión a la solicitud con id #'.$id_pqr,
                    'country' => $function->country,
                    'city' =>$function->city,
                    'lat' =>$function->lat,
                    'lon' =>$function->lon,
                ]);

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
        //dd($request);
        $email_user = $request->email;
        $consecutive_key = $request->consecutive_key;

        $pqr = Pqrs::where('email', $email_user)->where('consecutive_case', $consecutive_key)->exists();
            if ($pqr) {
                $pqr_info = Pqrs::where('email', $email_user)->where('consecutive_case', $consecutive_key)->first();
                $id_pqr = $pqr_info->consecutive_case;
                session()->flash('succeful_validateaccesspqr', $id_pqr);
                    return redirect()->route('pqrs.detalle_radicada', $id_pqr);
            }else{
                return redirect()->back()->with('error', 'El Correo ingresado no es el mismo con el que se radicó la solicitud');
            }

    }
    public function validacion($consecutive_case)
    {
        
        $dato = $consecutive_case;
        //dd($dato);
        return view('pqrs.includes.validateacces')->with('dato', $dato);

    }
}
