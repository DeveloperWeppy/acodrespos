<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LogsExport;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $now = Carbon::now()->addDay()->format('Y-m-d H:m:s');
        $logs_all = Log::whereDay('created_at', $now );

        if(isset($_GET['fromDate'],$_GET['toDate']) && $_GET['fromDate']!=""){
            $logs_all = $logs_all->whereDate('created_at',">=",$_GET['fromDate'])->whereDate('created_at',"<=",$_GET['toDate']);
        }

        if(isset($_GET['client_id']) && $_GET['client_id']!=""){
            $logs_all = $logs_all->where('user_id','=',$_GET['client_id']);
        }

        if(isset($_GET['module']) && $_GET['module']!=""){
            $logs_all = $logs_all->where('module',$_GET['module']);
        }

        if(isset($_GET['submodule']) && $_GET['submodule']!=""){
            $logs_all = $logs_all->where('submodule',$_GET['submodule']);
        }

        if(isset($_GET['action']) && $_GET['action']!=""){
            $logs_all = $logs_all->where('action',$_GET['action']);
        }

       


        if (isset($_GET['report'])) {
            $items=[];
            $k=1;
            foreach ($logs_all->get() as $key => $item) {

                $hora = date_format($item->created_at, 'h:i A');
                $item = [
                    'numero'=>$k,
                    'fecha'=>date_format($item->created_at, 'Y-m-d')." - ".$hora,
                    'usuario'=>$item->find($item->id)->usuario->name,
                    'modulo'=>$item->module,
                    'submodulo'=>$item->submodule,
                    'evento'=>$item->action,
                    'detalle'=>$item->detail,
                  ];
                array_push($items, $item);

                $k++;
            }

            return Excel::download(new LogsExport($items), 'listadoAuditoria_'.time().'.xlsx');
        }

        $logs_all = $logs_all->orderByDesc('id')->paginate(15);
        $users=User::all();

        $module = Log::select('module')->groupBy('module')->get();
        $subModule = Log::select('submodule')->groupBy('submodule')->get();
        $action = Log::select('action')->groupBy('action')->get();


        //dd($logs_all);
        return view('logs.index', compact('logs_all','users','module','subModule','action'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Log  $log
     * @return \Illuminate\Http\Response
     */
    public function show(Log $log)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Log  $log
     * @return \Illuminate\Http\Response
     */
    public function edit(Log $log)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Log  $log
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Log $log)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Log  $log
     * @return \Illuminate\Http\Response
     */
    public function destroy(Log $log)
    {
        //
    }
}
