
var notificacionIndes=1;
var arraynotificacion=[];
if(typeof urlNotificacion === 'undefined'){
  var urlNotificacion="notificacion";
}
function notifivisto(){
  
 $.ajax({
           headers: {
                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
                   },
           type: "get",
           encoding:"UTF-8",
           url: urlNotificacion+"/-1",
           processData: false,
           contentType: false,
           dataType:'json',
           beforeSend:function(){
           }
       }).done(function( respuesta ) {
         $("#notifCount").hide();
       }).fail(function( jqXHR,textStatus ) {
           
       });
}
function listnotificacion(index){
 $.ajax({
           headers: {
                 'X-CSRF-TOKEN': '{{ csrf_token() }}'
                   },
           type: "get",
           encoding:"UTF-8",
           url: urlNotificacion+"/"+index,
           processData: false,
           contentType: false,
           dataType:'json',
           beforeSend:function(){
           }
       }).done(function( respuesta ) {
         var itemIcon='<i class=" ni ni-single-02" style="font-size: 28px;"></i>';
         var conItem='<a href="/orders/%orderid%" class="row" style="margin:0px;margin-top:10px"><div class="col-2" style="display:flex;align-items:center;">%icon%</div> <span class="col-10" style="padding:0px;">%title%  <br><span style="font-size:11px">%body%</span> <br><span style="font-size:11px">%fecha%</span></span></a>';
         var listItem="";
         if(respuesta['totalNo']>0){
           $("#notifCount").html(respuesta['totalNo']);
           $("#notifCount").show();
         }else{
           $("#notifCount").hide();
         }
         
         if(respuesta['total']>0 && index>1){
           arraynotificacion=arraynotificacion.slice(0,((index-1)*10));
           arraynotificacion=arraynotificacion.concat(respuesta['data']);
         }else{
           arraynotificacion=respuesta['data'];
         }
         for (var i = 0; i < arraynotificacion.length ;i++) {
           if(arraynotificacion[i]['data']['title']=="Pedido rechazado"){
             itemIcon='<i class="fa fa-ban" style="color:#f80031;font-size: 28px;"></i>';
           }
           if(arraynotificacion[i]['data']['title']=="Su pedido ha sido aceptado"){
             itemIcon='<i class=" fa fa-check-circle-o" style="color:#03acca;font-size: 28px;"></i>';
           }
           if(arraynotificacion[i]['data']['title']=="Tu pedido está listo."){
             itemIcon='<i class=" fa fa-shopping-bag" style="color:#ff3709ca;font-size: 28px;"></i>';
           }
           if(arraynotificacion[i]['data']['title']=="Tu pedido ha sido entregado"){
             itemIcon='<i class="fa fa-handshake-o" style="color:#4fd69c;font-size: 28px;"></i>';
           }
           if(arraynotificacion[i]['data']['title']=="hay nueva orden"){
             itemIcon='<i class=" ni ni-basket text-orangse" style="color:#4fd69c;font-size: 28px;"></i>';
           }
           if(arraynotificacion[i]['data']['title']=="El tiempo de entrega fue modificado"){
            itemIcon='<i class=" ni ni-watch-time text-orangse" style="color:#4fd69c;font-size: 28px;"></i>';
           }
           var fecha=new Date(arraynotificacion[i]['created_at']).toLocaleString('en-US', { hour12: true });
           listItem+=conItem.replace('%orderid%',arraynotificacion[i]['data']['order_id']).replace('%icon%',itemIcon).replace('%title%', arraynotificacion[i]['data']['title']).replace('%body%', arraynotificacion[i]['data']['body']).replace('%fecha%', fecha);
         }
         if(respuesta['data'].length>0){
           if((notificacionIndes*10)<respuesta['total']){
             listItem+="<span class='btn' onclick='listNotificacionAumenta("+(index+1)+");event.stopPropagation();' style='width:100%'> Ver mas</span>";
           }
           $("#listNotif").html(listItem);
         }else{
           $("#listNotif").html("<span style='padding-left:10px'> No hay notificaciones</span>");
         }
         
       }).fail(function( jqXHR,textStatus ) {
           
       });
}
function listNotificacionAumenta(index){
 notificacionIndes=index;
  
 listnotificacion(index);
}

"use strict";
$(document).ready(function() {
    if($(".listNotif").length > 0 ) {
      /*
      Swal.fire({
        title: '',
        html: 'cargando datos...',
        button: false,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showCancelButton: false,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading()
            },
      });
      */
        listNotificacionAumenta(1);
    }
    // Enable pusher logging - don't include this in production
    if(PUSHER_APP_KEY){

        var audio = new Audio('https://soundbible.com/mp3/old-fashioned-door-bell-daniel_simon.mp3');

        Pusher.logToConsole = true;

        var pusher = new Pusher(PUSHER_APP_KEY, {
            cluster: PUSHER_APP_CLUSTER
        });

        var channel = pusher.subscribe('user.'+USER_ID);
        channel.bind('callwaiter-event', function(data) {
            js.notify(data.msg + " " + data.table.restoarea.name+" "+data.table.name,"primary");
            audio.play();
        });

        channel.bind('neworder-event', function(data) {
            if($("#listNotif").length > 0 ) {
                listNotificacionAumenta(1);
            }
            if(!data.order.ifclient){
                js.notify(data.msg + " Orden #" + data.order.id,"primary","onclick='javascript:location.href="+'"/orders/'+data.order.id+'"'+"'");
            }else{
                var type="";
                if(data.msg=="Pedido rechazado"){
                    type="warning";
                }
                js.notify(data.msg + ". Orden #" + data.order.id,type,"onclick='javascript:location.href="+'"/orders/'+data.order.id+'"'+"'");  
            }
            audio.play();
        });
    }
});


//js.notify("fffffffffff Orden ddd#1" ,{type:"primary"} ,"onclick='javascript:location.href='/orders/1'");  