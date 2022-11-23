<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\RestaurantClient;
use Illuminate\Http\Request;
use App\Restorant;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsExport;
use App\Exports\StaffExport;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner')) {
            if(auth()->user()->hasRole('owner')){
                $arrayId=[];
                $arrayFecha=[];
                $restaurant_id=0;
                if(auth()->user()->restaurant_id==null){
                    $restaurants=Restorant::where('user_id', auth()->user()->id)->get();
                    if(count($restaurants)>0){
                        $restaurant_id=$restaurants[0]->id;
                    }
                  
                }else{
                    $restaurant_id=auth()->user()->restaurant_id;
                }
                $client=RestaurantClient::where(['companie_id'=>$restaurant_id])->get();
                foreach ($client as $key => $Item){ 
                    array_push($arrayId,$Item->user_id);
                    array_push($arrayFecha,$Item->created_at);
                }
                $User=User::role('client')->whereIn('id', $arrayId)->where(['active'=>1])->orderBy('id','desc')->paginate(15);
                $arrayUser=$User;
                foreach ($User as $key => $Item){ 
                    $index =array_search($Item->id, $arrayId);
                    $arrayUser[$key]->created_at=$arrayFecha[$index];  
                }
                $User=$arrayUser;
            }else{
                $User=User::role('client')->where(['active'=>1])->orderBy('id','desc')->paginate(15);
            }
            
            //With downloaod
            if (isset($_GET['report'])) {

                $arrayId=[];
                $arrayFecha=[];
                $restaurant_id=0;
                if(auth()->user()->restaurant_id==null){
                    $restaurants=Restorant::where('user_id', auth()->user()->id)->get();
                    if(count($restaurants)>0){
                        $restaurant_id=$restaurants[0]->id;
                    }
                  
                }else{
                    $restaurant_id=auth()->user()->restaurant_id;
                }
                $client=RestaurantClient::where(['companie_id'=>$restaurant_id])->get();
                foreach ($client as $key => $Item){ 
                    array_push($arrayId,$Item->user_id);
                    array_push($arrayFecha,$Item->created_at);
                }
                $User=User::role('client')->whereIn('id', $arrayId)->where(['active'=>1])->get();
                $arrayUser=$User;
                foreach ($User as $key => $Item){ 
                    $index =array_search($Item->id, $arrayId);
                    $arrayUser[$key]->created_at=$arrayFecha[$index];  
                }
                $User=$arrayUser;


                $itemsForExport = [];
                foreach ($User as $key => $item) {
                    $item = [
                        'id'=>$item->id,
                        'name'=>$item->name,
                        'email'=>$item->email,
                        'phone'=>$item->phone,
                        'created'=>$item->created_at,
                    ];
                    array_push($itemsForExport, $item);
                }

                return Excel::download(new ClientsExport($itemsForExport), 'Clientes_'.time().'.xlsx');
            }

            //With downloaod
            if (isset($_GET['reportstaff'])) {

                $staff = $this->getRestaurant()->staff()->with('roles')->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'owner');
                })->paginate(config('settings.paginate'));

                $itemsForExport = [];
                foreach ($staff as $key => $item) {

                    foreach ($item->roles as $value){
                        $role = $value->name == 'staff' ? 'Mesero' : ($value->name == 'manager_restorant' ? 'Administrador de Restaurante' : 'Cocina');
                    }

                    $item = [
                        'id'=>$item->id,
                        'name'=>$item->name,
                        'email'=>$item->email,
                        'phone'=>$item->phone,
                        'role'=>$role,
                        'created'=>$item->created_at,
                    ];
                    array_push($itemsForExport, $item);
                }

                return Excel::download(new StaffExport($itemsForExport), 'staff_'.time().'.xlsx');
            }


            return view('clients.index', [
                    'clients' =>$User,
                ]
            );
        } else {
           return redirect()->route('orders.index')->withStatus(__('No Access'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listclients($tipo="")
    {
        $listClient=User::role('client')->where(['active'=>1])->get();
        if($tipo=="select"){
            $selectClient=array();
            $selectTelefono=array();
            foreach ($listClient as $key => $item) {
                    array_push($selectClient,array('id'=>$item->id,'text'=>$item->name." - ".$item->number_identification));
                    $selectTelefono[$item->id]=$item->phone;
            }
            return json_encode(array("selectTelefono"=>$selectTelefono,"selectClient"=>$selectClient));
        }
        return json_encode($listClient);
    }
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $client)
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('owner')) {
            return view('clients.edit', compact('client'));
        } else {
            return redirect()->route('orders.index')->withStatus(__('No Access'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $client)
    {
        $client->active = 0;
        $client->save();

        return redirect()->route('clients.index')->withStatus(__('Client successfully deleted.'));
    }
}
