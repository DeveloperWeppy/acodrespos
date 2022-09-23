{{-- @extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired')) --}}
@extends('layouts.app', ['title' => 'Página Expirada'])


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Página Expirada</h3>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm-12">
                            <h1 class="text-center text-primary">¡Ups!</h1>
                            <h3 class="text-center">La página a expirado...</h3>
                            <lottie-player src="{{asset('animations/419.json')}}" 
                             background="transparent"  speed="1"  style="width: 400px; height: 400px;"  loop autoplay></lottie-player>

                             
                        </div>
                    </div>
                    <div class="text-center">
                        <a class="btn btn-outline-primary text-center " href="{{route('login')}}"><i class="ni ni-tv-2 text-primary"></i> Volver al Inicio</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

    @section('js')

    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
        
    @endsection
@endsection