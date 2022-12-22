@extends('layouts.app', ['title' => __('Restaurants')])
@section('admin_title')
    {{ __('Reservas') }}
@endsection
@section('content')

<div class="header bg-gradient-info pb-6 pt-5 pt-md-8">
    <div class="container-fluid">

        <?php 
            
            $menuActivo = "cupones";
            if(isset($_GET)){
               $object = (object) $_GET;
               foreach ($object as $clave=>$valor) {
                    $menuActivo = $clave;
                }
            }
            
            
        ?>

        
        <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="res_menagment" role="tablist">

                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 {{ ($menuActivo=='cupones' ?'active':'') }}" id="tabs-menagment-main" data-toggle="tab" href="#cupones" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-badge mr-2"></i>Cupones</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 {{ ($menuActivo=='descuentos' ?'active':'') }}" id="tabs-menagment-main" data-toggle="tab" href="#descuentos" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-badge mr-2"></i>Descuentos</a>
                </li>
            </ul>
        </div>

    </div>
</div>


<div class="container-fluid mt--7 mb-5">
    <div class="row">
        <div class="col-12">
            <br />

            @include('partials.flash')



            <div class="tab-content" id="tabs">


                <!-- Tab Managment -->
                <div class="tab-pane fade show {{ ($menuActivo=='cupones' ?'active':'') }}" id="cupones" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    Cupones
                                </div>
                                <div class="col text-right">
                                    @isset($setup['action_link'])
                                        <a href="{{ $setup['action_link'] }}" class="btn btn-sm btn-primary">{{ __($setup['action_name'] ) }}</a>
                                        {{-- <a href="{{ $action_link }}" class="btn btn-sm btn-primary">Agregar Nuevo Personal</a> --}}
                                    @endisset
                                </div>
                            </div>
                        </div>
                        <div class="">


                            <div class="table-responsive">
                                <table class="table align-items-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">Nombre</th>
                                            <th scope="col">Cupon</th>
                                            <th class="table-web" scope="col">Valor</th>
                                            <th class="table-web" scope="col">Desde</th>
                                            <th class="table-web" scope="col">Hasta</th>
                                            <th scope="col">Cupones disponibles</th>
                                            <th scope="col">Usados</th>
                                            <th scope="col">Acción</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody id="listaReservas" style="background: #ffffff;">
                                        @include('coupons.includes.tablecoupon')
                                    
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="card-footer py-4">
                            @if(count($setup['items']))
                            <nav class="d-flex justify-content-end" aria-label="...">
                                {{ $setup['items']->appends(Request::all())->links() }}
                            </nav>
                            @else
                                <h4>No tienes reservas...</h4>
                            @endif
                        </div>
                        
                    </div>
                </div>


                <div class="tab-pane fade show {{ ($menuActivo=='descuentos' ?'active':'') }}" id="descuentos" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    Descuentos
                                </div>
                                <div class="col text-right">
                                        <a href="{{ route('admin.restaurant.coupons.createDiscount')}}" class="btn btn-sm btn-primary">Añadir Nuevo descuento</a>
                                </div>
                            </div>
                        </div>
                        <div class="">


                            <div class="table-responsive">
                                <table class="table align-items-center">
                                    <thead class="thead-light">
                                        <tr>
                                            <th scope="col">Nombre</th>
                                            <th class="table-web" scope="col">Valor</th>
                                            <th class="table-web" scope="col">Desde</th>
                                            <th class="table-web" scope="col">Hasta</th>
                                            <th scope="col">Descuento por</th>
                                            <th scope="col">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listaReservas" style="background: #ffffff;">
                                        @include('coupons.includes.tablediscount')
                                    
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <div class="card-footer py-4">
                            @if(count($setup['items']))
                            <nav class="d-flex justify-content-end" aria-label="...">
                                {{ $setup['discounts']->appends(Request::all())->links() }}
                            </nav>
                            @else
                                <h4>No tienes reservas...</h4>
                            @endif
                        </div>
                        
                    </div>
                </div>



            </div>
        </div>
    </div>
</div>



@endsection
