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
        $logs_all = Log::where('created_at', '<=', Carbon::today());

        if(isset($_GET['fromDate'],$_GET['toDate']) && $_GET['fromDate']!=""){
            $logs_all = $logs_all->whereDate('created_at',">=",$_GET['fromDate'])->whereDate('created_at',"<=",$_GET['toDate']);
        }

        if(isset($_GET['client_id']) && $_GET['client_id']!=""){
            $logs_all = $logs_all->where('user_id','=',$_GET['client_id']);
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

            return Excel::download(new LogsExport($items), 'logs_'.time().'.xlsx');
        }

        $logs_all = $logs_all->paginate(15);

        $users=User::all();
        //dd($logs_all);
        return view('logs.index', compact('logs_all','users'));
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
