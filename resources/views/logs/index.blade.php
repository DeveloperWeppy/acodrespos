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

                    <form method="GET" action="{{route('logs.index')}}">
                        <br>
                        <div class="tab-content orders-filters">
                                <div class="row">
                                    
                                    <div class="col-md-6">
                                        <div class="input-daterange datepicker row align-items-center">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Fecha de</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                        </div>
                                                        <input name="fromDate" class="form-control" placeholder="Fecha de" type="text" value="{{(isset($_GET['fromDate'])?$_GET['fromDate']:"")}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-control-label">Fecha hasta</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                        </div>
                                                        <input name="toDate" class="form-control" placeholder="Fecha hasta" type="text" value="{{(isset($_GET['toDate'])?$_GET['toDate']:"")}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                     <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label" for="client">Filtrar por usuario</label>
            
                                            <select class="form-control" id="client_id" name="client_id" >
                                                <option d value="" > -- Seleccione una opción -- </option>
                                                @foreach($users as $key)
                                                    <option value="{{$key->id}}" <?php if(isset($_GET['client_id'])&&$_GET['client_id'].""==$key->id.""){echo "selected";} ?> >{{$key->number_identification}} - {{$key->name}}</option>
                                                @endforeach
                                                </select>
                                        </div>
                                    </div>
                                                             
                                                            
                                </div>
            
                                <div class="col-md-6 offset-md-6">
                                    <div class="row">
                                            <div class="col-md-8 text-right">
                                                <a href="http://www.testpost.com/orders?report=true" class="btn btn-md btn-success">Descargar informe</a>
                                            </div>
                                        
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary btn-md btn-block">Filtrar</button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </form>
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