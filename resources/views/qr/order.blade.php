@extends('layouts.frontqr', ['data' => $data->client])
@section('content')
<div class="row justify-content-center">
    <div class="card shadow border-0 mt-8">
        <div class="card-body text-center">
            <div class="justify-content-center text-center">
                @if ($data->status->pluck('alias')->last()=="accepted_by_admin")
                 <lottie-player  class="lottieactiva" src="{{asset('animations/received.json');}}" background="transparent" speed="1" style=" height: 200px;" loop  autoplay=""></lottie-player>
                @endif
                @if ($data->status->pluck('alias')->last()=="accepted_by_restaurant")
                 <lottie-player class="lottieactiva" src="{{asset('animations/preparing.json');}}" background="transparent" speed="1" style=" height: 200px;" loop autoplay=""></lottie-player>
                @endif
                @if ($data->status->pluck('alias')->last()=="prepared")
                <lottie-player class="lottieactiva" src="{{asset('animations/serving.json');}}" background="transparent" speed="1" style=" height: 200px;" loop  autoplay=""></lottie-player>
                @endif
                @if ($data->status->pluck('alias')->last()=="rejected_by_restaurant")
                <lottie-player class="lottieactiva" src="{{asset('animations/rejected.json');}}" background="transparent" speed="1" style=" height: 200px;" loop  autoplay=""></lottie-player>
                @endif
                @if ($data->status->pluck('alias')->last()=="picked_up")
                <lottie-player class="lottieactiva" src="{{asset('animations/picked_up.json');}}" background="transparent" speed="1" style=" height: 200px;" loop  autoplay=""></lottie-player>
                @endif
                @if ($data->status->pluck('alias')->last()=="delivered")
                  <lottie-player  class="lottieactiva" src="{{asset('animations/delivered.json');}}" background="transparent" speed="1" style=" height: 200px;" loop  autoplay=""></lottie-player>
                @endif
            </div>
            <h2 class="display-2 titlestatus">¡{{__($data->status->pluck('name')->last()) }}!</h2>
            <h1 class="mb-4">
                <span class="badge badge-primary">{{ __('Orden')." #".$data->id }}</span>
            </h1>
            <div class="d-flex justify-content-center">
                <div class="col-8">
                    <h5 class="mt-0 mb-5 heading-small text-muted textstatus">
                        @if ($data->status->pluck('alias')->last()=="accepted_by_admin")
                             Su pedido está creado. Se le notificará para obtener más información.
                        @endif
                        @if ($data->status->pluck('alias')->last()=="accepted_by_restaurant")
                              El pedido esta en preparacion .
                        @endif
                        @if ($data->status->pluck('alias')->last()=="prepared")
                              El pedido esta preparado .
                        @endif
                        @if ($data->status->pluck('alias')->last()=="rejected_by_restaurant")
                             El pedido fue rechazado por el restaurante .
                        @endif
                        @if ($data->status->pluck('alias')->last()=="picked_up")
                              El pedido fue entregado al conductor.
                        @endif
                        @if ($data->status->pluck('alias')->last()=="delivered")
                             El pedido ha sido entregado.
                        @endif
                    </h5>
                    <div class="font-weight-300 mb-5">
                        Gracias por su compra, 
                    <span class="h3">{{$data->restorant->name}}</span></div>
                    <a href="{{ route('vendor',$data->restorant->subdomain) }}" class="btn btn-outline-primary btn-sm">{{ __('Go back to restaurant') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    
    const player = document.querySelector("lottie-player");
    var statusini="{{$data->status->pluck('alias')->last()}}";
    var arrayorderstarus={
             accepted_by_admin:{lottie:"{{asset('animations/received.json');}}",text:"Su pedido está creado. Se le notificará para obtener más información.",title:"Aceptado por el administrador"},
             accepted_by_restaurant:{lottie:"{{asset('animations/preparing.json');}}",text:"El pedido esta en preparacion .",title:"Aceptado por el restaurante"},
             prepared:{lottie:"{{asset('animations/serving.json');}}",text:"La espera término, acercate al mostrador.",title:"Preparado"},
             rejected_by_restaurant:{lottie:"{{asset('animations/rejected.json');}}",text:" El pedido fue rechazado por el restaurante .",title:"Rechazado"},
             assigned_to_driver:{lottie:"{{asset('animations/picked_up.json');}}",text:"El pedido fue entregado al conductor",title:"Asignado al conductor"},
             delivered:{lottie:"{{asset('animations/delivered.json');}}",text:"Finalizo el pedido, ha sido entregado ",title:"Entregado"},
        };
    function orderStatus(){
        var audio = new Audio('https://soundbible.com/mp3/old-fashioned-door-bell-daniel_simon.mp3');
        $.ajax({
                type:'get',
                url:' {{ route('orderstatus',['restorant'=>$data->restorant->subdomain,"id"=>$data->id]) }}',
                dataType: 'json',
                headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                success:function(response){
                    if(response.status){
                        if(response.finalstate!=statusini){
                            statusini=response.finalstate;
                            $(".titlestatus").html(arrayorderstarus[statusini].title);
                            $(".textstatus").html(arrayorderstarus[statusini].text);
                            player.load(arrayorderstarus[statusini].lottie);
                            audio.play();
                        }

                    }
                }
        });
    }
    setInterval(orderStatus, 3000);
</script>
@endsection





