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
    public function get(){
        $dompdf = new Dompdf();
        $dompdf->loadHtml(view('pdf.invoice.70mm',array()));
        
        $dompdf->set_paper(array(0,0, 119,1000));
        $dompdf->set_option('dpi', 42);
        $dompdf->render();
        
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=documento.pdf");
        echo $dompdf->output();
        exit();
    }
    public function pdf(){

    }
}
