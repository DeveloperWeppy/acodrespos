<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeoZoneDelivery;
class GeoZoneDeliveryController extends Controller
{
    public function get()
    {
        $data=GeoZoneDelivery::where('restorant_id',auth()->user()->restorant->id)->get();
        $tabla="";
        $arrayzonep=array();
        foreach ($data as $clave => $zona) {
              $jzone=json_decode($zona->radius);
               array_push($arrayzonep,array("color"=>$zona->colorarea,"radius"=>$jzone->cd));
               $estado=$zona->active==1?'Habilitado':'Deshabilitado';
               $tabla.='<tr>
                <td>'.$zona->name.'</td>
                <td>'.$zona->price.'</td>
                <td><input type="color" id="favcolor" name="favcolor" value="'.$zona->colorarea.'"></td>
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
        $data_zone=array( 'name' => $request->name,'radius' => $request->radius,'price'=>$request->price,'restorant_id'=>auth()->user()->restorant->id, 'colorarea' =>$request->colorarea,'active' =>$request->active);
        if ($d_zone = GeoZoneDelivery::create($data_zone)) {
            return array("data"=>$d_zone,"mensaje"=>true);
        }
        return array("mensaje"=>false);
    }
    public function updated($id,Request $request)
    {
        $data_zone=array( 'name' => $request->name,'price'=>$request->price,'restorant_id'=>auth()->user()->restorant->id, 'colorarea' =>$request->colorarea,'active' =>$request->active);
        if($request->has('radius')) {
            if($request->radius!=null){
                $data_zone['radius'] = $request->radius;
            }
        }
        GeoZoneDelivery::findOrFail($id)->update($data_zone);
        return array("data"=>"","table"=>"","mensaje"=>true);
    }
    public function destroy($id)
    {
        GeoZoneDelivery::destroy($id);
    }
    public function getgeo(){
    }
}
