
<?php 
  $convert=config('settings.do_convertion');
  $currency=config('settings.cashier_currency');
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<style>
  @page { margin-top: 0px; margin-bottom: 0px;font-family: 'Roboto', sans-serif; }
  table, tr, td {
              border:0px;
              border-collapse: collapse;
            }
  html{margin:0;padding:0;font-family: 'Roboto', sans-serif;font-size:8px;}
  table{margin:0;padding:0;width:100%;}
  .titulo{
     
  }
</style>
<div style="text-align: center;font-weight: bold;">APP {{strtoupper(config('app.name'))}}</div>
<div style="width:100%;">
    <center>
      <div style="padding-top:10px">
        <div style="font-weight: bold;">{{strtoupper($order->restorant->name)}} </div>
        <div style="font-weight: bold;">NIT: {{strtoupper($order->restorant->nit)}}</div>
        <div style="font-weight: bold;"> {{strtoupper($order->restorant->address)}}</div>
      </div>
    </center> 
    <hr>
    <div>FACTURA DE VENTA NO.{{strtoupper($order->prefix_consecutive.$order->consecutive)}} </div>
    @if(isset($order->table->name))
      <div>MESA: {{strtoupper($order->table->name)}}</div>
      <div>PERSONAS: {{$order->number_people}}</div>
    @endif

    <div>ORDEN: #{{$order->id}}</div>
    <div>FECHA: {{$order->created_at}}</div>
    <div>MESERO: {{strtoupper($mesero)}}</div>
    <hr>
    <table>
        <tr>
          <td style="text-align: left;">CANT</td> 
          <td style="text-align: left;">DESCRIPCION</td>
          <td style="text-align: right;">TOTAL</td>
        </tr>
        @foreach ($items as $item)
          <?php 
              $theItemPrice= ($item->pivot->variant_price?$item->pivot->variant_price:$item->price);
          ?>
          <tr>
               <td>{{$item->pivot->qty}}</td>
               <td>{{strtoupper($item->name)}}</td>
               <td>@money( $item->pivot->qty*$theItemPrice, $currency,true)</td>
          </tr> 
        @endforeach
    </table> 
    <hr>
    <table style="">
        <tr>
          <td style="text-align: right;">SUBTOTAL: </td> 
          <td style="text-align: right;">@money( $order->delivery_price+$order->order_price_with_discount, $currency,true)</td>
        </tr>
        <tr>
          <td style="text-align: right;">PROPINA:</td>
          <td style="text-align: right;">@money($order->propina, $currency,true) </td> 
        </tr>
        <tr>
          <td style="text-align: right;">TOTAL</td>
          <td style="text-align: right;">@money( $order->propina+$order->delivery_price+$order->order_price_with_discount, $currency,true) </td> 
        </tr>
    </table> 
    <br>
    @if( $qrcode!="")
        <div style="text-align: center;"><img src="{{$qrcode}}" style="width:65px;heigth:auto;"></div>
    @endif
        <div style="text-align: center;">{{strtoupper($order->restorant->invoice_footer)}}</div>
</div>