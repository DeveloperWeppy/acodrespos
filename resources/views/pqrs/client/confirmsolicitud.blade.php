@extends('layouts.app', ['title' => ''])
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card shadow border-0 mt-4 pt-2">
            <div class="card-body text-center">
                <div class="justify-content-center text-center">
                    <lottie-player src="https://assets5.lottiefiles.com/packages/lf20_y2hxPc.json"  background="transparent"  speed="1"  style=" height: 90px;"    autoplay></lottie-player>
                </div>
                <h2 class="display-2">{{ __("¡PQR Recibido!") }}</h2>
                <h1 class="mb-4">
                    <span class="badge badge-primary">{{ __('Número del Caso: ').$get_pqr->consecutive_case	 }}</span>
                </h1>
                <div class="d-flex justify-content-center">
                    <div class="col-8">
                        <h5 class="mt-0 mb-5 heading-small text-muted">
                            {{ __("Para nosotros es muy importante la información que nos acabas de compartir, por eso nuestro equipo de servicio al cliente la estará revisando
                             y notificandote a la brevedad posible el estado de tu solicitud.") }}
                        </h5>
                        <div class="font-weight-300 mb-5">
                            <span class="h3 text-capitalize">{{ $get_pqr->name }},</span>
                                {{ __("recuerda guardar el número de tu caso para hacerle seguimiento.") }}
                        </div>
                        <a href="javascript:history.back()" class="btn btn-outline-primary btn-sm">{{ __('Regresar') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection





