@extends('layouts.app', ['title' => __('PQRS')])

@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
</div>
<div class="container-fluid mt--9">
    <div class="row">
        <div class="col-xl-12 order-xl-1">
            <div class="card bg-secondary shadow">
                <div class="card-header bg-white border-0">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h3 class="mb-0 text-capitalize">{{ __('Logs de Auditoría') }}</h3>
                            
                        </div>
                        <div class="col-6 text-right">
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-12">
                        </div>
                    </div>
                    @if(count($logs_all)>0)
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush dataTable-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>VER</th>
                                        <th>Fecha y Hora</th>
                                        <th>Usuario</th>
                                        <th>Módulo</th>
                                        <th>Submodulo</th>
                                        <th>Evento</th>
                                        <th>Detalle del Evento</th>
                                    </tr>                                    
                                </thead>
                                <tbody>
                                    @foreach ($logs_all as $item)
                                    @php
                                        $hora = date_format($item->created_at, 'h:i A');
                                    @endphp
                                    <tr>
                                        <td>
                                            <button type="button" class="btn badge badge-success badge-pill"><i class="fas fa-eye"></i></button>
                                        </td>
                                        <td>{{date_format($item->created_at, 'Y-m-d')}} - {{$hora}}</td>
                                        <td>{{$item->find($item->id)->usuario->name}}</td>
                                        <td>{{$item->module}}</td>
                                        <td>{{$item->submodule}}</td>
                                        <td>{{$item->action}}</td>
                                        <td>{{$item->detail}}</td>
                                    </tr>
                                    @endforeach
                                    
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer py-4">
                            @if(count($logs_all))
                            <nav class="d-flex justify-content-end" aria-label="...">
                                {{ $logs_all->appends(Request::all())->links() }}
                            </nav>
                            @else
                                <h4>{{ __('No hay resultados') }} ...</h4>
                            @endif
                        </div>
                        @else
                        <div class="text-center">
                            <h3>Parece que no hay registros de Eventos aún...</h3>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">
        $( document ).ready(function() {
        });
    </script>
@endsection

@endsection