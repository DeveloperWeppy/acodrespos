<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use App\User;
use App\Order;
use App\Restorant;
use App\Models\Orderitems;
class PdfController extends Controller
{
    public function get($id,$tipo=0){
        $order = Order::findOrFail($id);
        $mesero=User::find($order->employee_id);
        $items=$order->items()->get();
        $ifprint=true;
        $maxPrint=0;
        $newItem=array();
        $arrayId=array();
        if($tipo>0 && $tipo<5){
            foreach ($items as $item) {
                if($item->pivot->print==0){
                  $ifprint=false;
                  array_push($newItem, $item);
                  array_push($arrayId, $item->pivot->id);
                }
                if($maxPrint<$item->pivot->print){
                 $maxPrint=$item->pivot->print;
                }
             }
             if(!$ifprint){
                 if($tipo==1){
                    $items=$newItem;
                 }
                 $printC=$maxPrint+1;
                 $oi=Orderitems::whereIn('id', $arrayId)->where('print',0)->update(['print' =>$printC ]);
             }
             if($ifprint && $tipo==1 ){
                 foreach ($items as $item) {
                     if($item->pivot->print==$maxPrint){
                         array_push($newItem, $item);
                     }
                 }
                 $items=$newItem;
             }  
        }
        $alto=300+(count($items)*25)+100;
        $dompdf = new Dompdf();
        if($order->restorant->invoice_size=="" || $order->restorant->invoice_size=="80mm" ){
            $ancho=198;
            $dpi=70;
        }
        if($order->restorant->invoice_size=="58mm"){
          $alto=300+(count($items)*25)+150;
          $ancho=119;
          $dpi=42;
        }
        $mesero="";
        if(isset($mesero->name)){
            $mesero=$mesero->name;
        }
        if($tipo>0 &&  $tipo<5){
            $dompdf->loadHtml(view('pdf.command.'.$order->restorant->invoice_size,array("order"=> $order,"mesero"=>$mesero,"items"=>$items)));
        }else{
            $dompdf->loadHtml(view('pdf.invoice.'.$order->restorant->invoice_size,array("order"=> $order,"mesero"=>$mesero,"items"=>$items)));
        }  
        $dompdf->set_paper(array(0,0,$ancho, $alto));
        $dompdf->set_option('dpi', $dpi);
        $dompdf->render();
        
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=documento.pdf");
        echo $dompdf->output();
        exit();
    }
    public function pdf(){
        $alto=300+(count($items)*25)+100;
        $dompdf = new Dompdf();
        if($order->restorant->invoice_size=="" || $order->restorant->invoice_size=="80mm" ){
            $ancho=198;
            $dpi=70;
        }
        if($order->restorant->invoice_size=="58mm"){
          $alto=300+(count($items)*25)+150;
          $ancho=119;
          $dpi=42;
        }
        $mesero="";
        if(isset($mesero->name)){
            $mesero=$mesero->name;
        }
        if($tipo==1){
            $dompdf->loadHtml(view('pdf.command.'.$order->restorant->invoice_size,array("order"=> $order,"mesero"=>$mesero,"items"=>$items)));
        }else{
            $dompdf->loadHtml(view('pdf.invoice.'.$order->restorant->invoice_size,array("order"=> $order,"mesero"=>$mesero,"items"=>$items)));
        }  
        $dompdf->set_paper(array(0,0,$ancho, $alto));
        $dompdf->set_option('dpi', $dpi);
        $dompdf->render();
        
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=documento.pdf");
        echo $dompdf->output();
    }
}
