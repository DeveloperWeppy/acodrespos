@extends('layouts.app', ['title' => ''])
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="card shadow border-0 mt-8">
            <div class="card-body text-center">
                <div class="justify-content-center text-center">
                    <lottie-player src="https://assets5.lottiefiles.com/packages/lf20_y2hxPc.json"  background="transparent"  speed="1"  style=" height: 200px;"    autoplay></lottie-player>
                </div>
                <h2 class="display-2">{{ __("¡Solicitud Enviada Exitosamente!") }}</h2>
                <h1 class="mb-4">
                    <span class="badge badge-primary">{{ __('Número del Caso: ').$get_pqr->consecutive_case	 }}</span>
                </h1>
                <div class="d-flex justify-content-center">
                    <div class="col-8">
                        <h5 class="mt-0 mb-5 heading-small text-muted">
                            {{ __("Su solicitud") }}
                        </h5>
                        <div class="font-weight-300 mb-5">
                            {{ __("Thanks for your purchase") }}, 
                        <span class="h3">{{ $get_pqr->name }}</span></div>
                        <a href="javascript:history.back()" class="btn btn-outline-primary btn-sm">{{ __('Regresar') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection





