
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
<div style="text-align: center;font-weight: bold;">APP {{strtoupper(config('app.name'))}}</div>
<div style="width:100%;">
    <center>
      <div style="padding-top:1px">
        <div style="font-weight: bold;">{{strtoupper($order->restorant->name)}} </div>
      </div>
    </center> 
    <hr>
    <div>ORDEN: #{{$order->id}}</div>
    @if(isset($order->table->name))
      <div>MESA: {{strtoupper($order->table->name)}}</div>
    @endif
    <div>FECHA: {{$order->created_at}}</div>
    <hr>
    <table>
        <tr>
          <td style="text-align: left;">CANT</td> 
          <td style="text-align: left;">DESCRIPCION</td>
        </tr>
    </table>
    
        @foreach ($items as $item)
          <table>
          <?php 
              $theItemPrice= ($item->pivot->variant_price?$item->pivot->variant_price:$item->price);
          ?>
          <tr>
               <td>{{$item->pivot->qty}}</td>
               <td>{{strtoupper($item->name)}}</td>
          </tr> 
          </table>
          <?php if($item->pivot->item_observacion!=""){ ?>
            <div >OBSER: {{strtoupper($item->pivot->item_observacion)}}</div>
          <?php  }?>
        @endforeach
        <?php if($order->comment!=""){ ?>
          <div >OBSER GEN: {{strtoupper($order->comment)}}</div>
        <?php  }?>
    </table> 
   
    
    <br>
    
 
</div>