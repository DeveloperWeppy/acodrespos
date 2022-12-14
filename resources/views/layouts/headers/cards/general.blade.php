<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    <div class="container-fluid">
        <div class="header-body">
            <!-- Card stats -->
            <div class="row">

                <div class="col-12 col-lg-6 col-xl-4 mt-3">
                    <div class="card card-stats mb-0 mb-xl-0" style="min-height:103px">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Orders') }} ( 30 {{ __('days') }} )</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ $last30daysOrders }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                        <i class="fas fa-shopping-basket"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="col-12 col-lg-6 col-xl-4 mt-3">
                    <div class="card card-stats mb-0 mb-xl-0" style="min-height:103px">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Sales Volume') }} ( 30 {{ __('days') }} )</h5>
                                    <span class="h2 font-weight-bold mb-0"> @money( is_numeric($last30daysOrdersValue['order_price'])?$last30daysOrdersValue['order_price']:0, config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
               

                <div class="col-12 col-lg-6 col-xl-4 mt-3">
                    <div class="card card-stats mb-0 mb-xl-0" style="min-height:103px">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Clientes Adquiridos (30 D??as)</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ $last30daysClientsRestaurant }}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 col-xl-4 mt-3">
                    <div class="card card-stats mb-0 mb-xl-0" style="min-height:103px">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Visitas al Perfil</h5>
                                    <span class="h2 font-weight-bold mb-0">{{ $allViews }} </span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
           

           
            @if(auth()->user()->hasRole('admin'))
                @if(config('app.isft'))
              

                    <div class="col-12 col-lg-6 col-xl-4 mt-3">
                        <div class="card card-stats mb-0 mb-xl-0" style="min-height:103px">
                            <div class="card-body">
                                @if(auth()->user()->hasRole('admin'))
                                    <div class="row">
                                        <div class="col" style="width: 60%;">
                                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Number of restaurants') }}</h5>
                                            <span class="h2 font-weight-bold mb-0" style="white-space: nowrap;">{{ $countItems }} {{ __('restaurants') }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                                <i class="fas fa-folder"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if(auth()->user()->hasRole('owner')&&!$doWeHaveExpensesApp)
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Number of items') }}</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ $countItems }} {{ __('items') }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                                <i class="fas fa-folder"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if($doWeHaveExpensesApp)
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Expenses') }} ( 30 {{ __('days') }} )</h5>
                                            <span class="h2 font-weight-bold mb-0">@money( is_numeric($expenses['last30daysCostValue'])?$expenses['last30daysCostValue']:0, config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                                <i class="fas fa-chart-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endif 
    
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 col-xl-4 mt-3">
                        <div class="card card-stats mb-0 mb-xl-0" style="min-height:103px">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Envios a domicilio ( 30 {{ __('days') }} )</h5>
                                        <span class="h2 font-weight-bold mb-0"> @money(is_numeric($last30daysDeliveryFee)?$last30daysDeliveryFee:0, config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                            <i class="fas fa-motorcycle"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 col-xl-4 mt-3">
                        <div class="card card-stats mb-0 mb-xl-0" style="min-height:103px">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Total propinas (30 D??as)</h5>
                                        <span class="h2 font-weight-bold mb-0">@money(is_numeric($last30daysStaticFee)?$last30daysStaticFee:0, config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 col-xl-4 mt-3">
                        <div class="card card-stats mb-0 mb-xl-0" style="min-height:103px">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">Total en impoconsumo </h5>
                                        <span class="h2 font-weight-bold mb-0">@money(is_numeric($last30daysDynamicFee)?$last30daysDynamicFee:0, config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- 
                    <div class="col-xl-3 col-lg-6">
                        <div class="card card-stats mb-0 mb-xl-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title text-uppercase text-muted mb-0">{{ __('Total Fee') }} ( 30 {{ __('days') }} )</h5>
                                        <span class="h2 font-weight-bold mb-0">@money(( is_numeric($last30daysTotalFee) ? $last30daysTotalFee:0), config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                                    </div>
                                    <div class="col-auto">
                                        <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                            <i class="fas fa-coins"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    --}}
            
                @endif
            @endif

            </div>
        </div>
    </div>
</div>
