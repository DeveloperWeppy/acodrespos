<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use App\Models\GeoZoneDelivery;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GeoZoneDeliveryController extends Controller
{
    public function get()
    {
        $data=GeoZoneDelivery::where('restorant_id',auth()->user()->restorant->id)->get();
        $tabla="";
        $geoZone="";
        $arrayzonep=array();
        foreach ($data as $clave => $zona) {
              $jzone=json_decode($zona->radius);
                if(isset($jzone->cd)){
                    $geoZone=$jzone->cd;
                }else{
                    $arraykey= array_keys((array) $jzone);
                    $geoZone=$arraykey[0];
                    $geoZone= $jzone->$geoZone;
                }
               array_push($arrayzonep,array("color"=>$zona->colorarea,"radius"=>$geoZone));
               $estado=$zona->active==1?'Habilitado':'Deshabilitado';
               $tabla.='<tr>
                <td>'.$zona->name.'</td>
                <td>'.$zona->price.'</td>
                <td><span id="favcolor" class="p-1" style=" background-color:'.$zona->colorarea.'"></span></td>
                <td>'.$estado.'</td>
                <td>'.$zona->created_at.'</td>
                <td>
                    <button type="button" class="btn btn-success geoedit"  data='."'".'["'.$zona->id.'","'.$zona->name.'","'.$zona->colorarea.'","'.$zona->active.'","'.$zona->price.'"]'."'".' > <i class="fa fa-pencil-square-o" aria-hidden="true"></i> </button>
                   
                    <button type="button" class="btn btn-danger geodelet" data-id="'.$zona->id.'"><i class="fa fa-trash" aria-hidden="true"></i></button>
                </td>
              </tr>';
        }
        return array("data"=>$arrayzonep,"table"=>$tabla,"mensaje"=>true);
    }
    public function store(Request $request)
    {
        $function = $this->getIpLocation();
        $data_zone=array( 'name' => $request->name,'radius' => $request->radius,'price'=>$request->price,'restorant_id'=>auth()->user()->restorant->id, 'colorarea' =>$request->colorarea,'active' =>$request->active);
        if ($d_zone = GeoZoneDelivery::create($data_zone)) {
            Log::create([
                'user_id' => Auth::user()->id,
                'ip' => $request->ip(),
                'module' => 'RESTAURANTES',
                'submodule' => 'AREAS DE ENTREGA',
                'action' => 'Registro',
                'detail' => 'Registro de Nueva Área de Entrega, ' .$request->name,
                'country' => $function->country,
                'city' =>$function->city,
                'lat' =>$function->lat,
                'lon' =>$function->lon,
            ]);
            return array("data"=>$d_zone,"mensaje"=>true);
        }
        return array("mensaje"=>false);
    }
    public function updated($id,Request $request)
    {
        $function = $this->getIpLocation();
        $data_zone=array( 'name' => $request->name,'price'=>$request->price,'restorant_id'=>auth()->user()->restorant->id, 'colorarea' =>$request->colorarea,'active' =>$request->active);
        if($request->has('radius')) {
            if($request->radius!=null){
                $data_zone['radius'] = $request->radius;
            }
        }
        GeoZoneDelivery::findOrFail($id)->update($data_zone);
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $request->ip(),
            'module' => 'RESTAURANTES',
            'submodule' => 'AREAS DE ENTREGA',
            'action' => 'Actualización',
            'detail' => 'Se actualizó el Área de Entrega, ' .$request->name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        return array("data"=>"","table"=>"","mensaje"=>true);
    }
    public function destroy($id)
    {
        $function = $this->getIpLocation();
        GeoZoneDelivery::destroy($id);
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $function->ip,
            'module' => 'RESTAURANTES',
            'submodule' => 'AREAS DE ENTREGA',
            'action' => 'Eliminación',
            'detail' => 'Se eliminó el Área de Entrega',
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
    }
    public function getgeo(){
    }
}
