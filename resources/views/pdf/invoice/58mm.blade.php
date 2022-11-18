
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
  html{margin:0;padding:0;font-family: 'Roboto', sans-serif;font-size:4px;}
  table{margin:0;padding:0;width:100%;}
  .titulo{
     
  }
</style>
<div style="width:100%;">
    <center>
      <div style="padding-top:10px">
        <div>{{$order->restorant->name}} </div>
        <div>NIT: {{$order->restorant->nit}}</div>
        <div> {{$order->restorant->address}}</div>
      </div>
    </center> 
    <hr>
    <div>MESA:{{$order->table->name}}</div>
    <div>PERSONAS:8</div>
    <div>ORDEN:{{$order->id}}</div>
    <div>FECHA:{{$order->created_at}}</div>
    <div>MESERO:{{$mesero}}</div>
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
               <td>{{$item->name}}</td>
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
    
    <div >{{$order->restorant->invoice_footer}}</div>
</div>