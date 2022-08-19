<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\RestaurantClient;
use Illuminate\Http\Request;
use App\Restorant;
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
                }
                
                $User=User::role('client')->whereIn('id', $arrayId)->where(['active'=>1])->paginate(15);
            }else{
                $User=User::role('client')->where(['active'=>1])->paginate(15);
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
