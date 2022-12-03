<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use App\User;
use App\Order;
use App\Restorant;
class PdfController extends Controller
{
    public function get($id){
        $order = Order::findOrFail($id);
        $mesero=User::find($order->employee_id);
        $items=$order->items()->get();
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
        $dompdf->loadHtml(view('pdf.invoice.'.$order->restorant->invoice_size,array("order"=> $order,"mesero"=>$mesero,"items"=>$items)));
        
        $dompdf->set_paper(array(0,0,$ancho, $alto));
        $dompdf->set_option('dpi', $dpi);
        $dompdf->render();
        
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=documento.pdf");
        echo $dompdf->output();
        exit();
    }
    public function pdf(){

    }
}
