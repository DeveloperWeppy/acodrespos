<?php

namespace App\Http\Controllers;

use App\Restorant;
use App\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QRController extends Controller
{
    public function index()
    {
        $domain = config('app.url');
        $linkToTheMenu = $domain.'/'.config('settings.url_route').'/'.auth()->user()->restorant->subdomain;

        if(strlen(auth()->user()->restorant->getConfig('domain'))>3){
            $linkToTheMenu = "https://".auth()->user()->restorant->getConfig('domain');
        }else if (config('settings.wildcard_domain_ready')) {
            $linkToTheMenu = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https://' : 'http://').auth()->user()->restorant->subdomain.'.'.str_replace('www.', '', $_SERVER['HTTP_HOST']);
        }

        $dataToPass = [
            'url'=>$linkToTheMenu,
            'titleGenerator'=>__('Restaurant QR Generators'),
            'selectQRStyle'=>__('SELECT QR STYLE'),
            'selectQRColor'=>__('SELECT QR COLOR'),
            'color1'=>__('Color 1'),
            'color2'=>__('Color 2'),
            'titleDownload'=>__('QR Downloader'),
            'downloadJPG'=>__('Download JPG'),
            'titleTemplate'=>__('Menu Print template'),
            'downloadPrintTemplates'=>__('Download Print Templates'),
            'templates'=>explode(',', config('settings.templates')),
            'linkToTemplates'=>env('linkToTemplates', '/impactfront/img/templates.zip'),
        ];

        return view('qrsaas.qrgen')->with('data', json_encode($dataToPass));
    }
    public function showOrder($id)
    {
        $order = Order::findOrFail($id);
        return view('qr.order')->with('data',$order);
    }
    public function orders($restorant,$id=0)
    {
        $data= array();
        if($id!=0){
           $data = Order::findOrFail($id);
           $data->finalstate= $data->status->pluck('alias')->last();
    
        }else{
            $rest = Restorant::where(['subdomain'=>$restorant])->get();
            if(count($rest)>0){
                $data = Order::where(['restorant_id'=>$rest[0]->id])->where('created_at', '>=', Carbon::today())->orderBy('created_at', 'desc'); 
            }
           
        }
        return json_encode($data);
    }
    
}
