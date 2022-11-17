@extends('layouts.app')
@section('admin_title')
    {{__('Dashboard')}}
@endsection

@section('content')
    @if(!auth()->user()->hasRole('driver'))
        @include('layouts.headers.cards.general')
    @else
        @include('layouts.headers.cards.driver')
    @endif

    @if(
        (auth()->user()->hasRole('admin')&&config('app.isft')) ||
        (auth()->user()->hasRole('owner')&&in_array("drivers", config('global.modules',[]))) 
    )
      
        <div class="container-fluid mt--7 mb-8">
            <div class="row">
                <div class="col-xl-12">
                    @include('drivers.map')
                </div>
            </div>
        </div>  
    @endif
    

    @if(!auth()->user()->hasRole('driver'))
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-8 mb-5 mb-xl-0">
                <div class="card bg-gradient-default shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-light ls-1 mb-1">{{ __('Overview') }}</h6>
                                <h2 class="text-white mb-0">{{ __('Sales value') }}</h2>
                            </div>

                        </div>
                    </div>
                    <script>
                        var salesValue= @json($salesValue);
                        var monthLabels = @json($monthLabels);
                        
                        totalOrders=[];
                        salesValues=[];
                        costValues=[];
                        for (const key in salesValue) {
                            totalOrders.push(salesValue[key].totalPerMonth);
                            salesValues.push(salesValue[key].sumValue);
                            /* if(salesValue[key].costValue){
                                costValues.push(salesValue[key].costValue);
                            }else{
                                costValues.push(0);
                            } */
                        }
                    </script>

                    <div class="card-body">
                        <!-- Chart -->
                        @if(count($salesValue)>0)
                            <div class="chart">
                                <!-- Chart wrapper -->
                                <canvas id="chart-sales" class="chart-canvas"></canvas>
                            </div>
                        @else
                            <p class="text-white">{{ __('No hay ventas en este momento!') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">{{ __('Ranking por Meses') }}</h6>
                                <h2 class="mb-0">{{ __('Pedidos Totales') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart -->
                        @if(count($salesValue)>0)
                            <div class="chart">
                                <canvas id="chart-orders" class="chart-canvas"></canvas>
                            </div>
                        @else
                            <p>{{ __('No hay ventas en este momento!') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

      

        @if ($doWeHaveExpensesApp)
        <script>
            var nameproducts = [];
            var cantidadproducts = [];

            var namedias = [];
            var totalventas7dias = [];
            
            var categoriesLabels = {!! json_encode($expenses['last30daysCostPerGroupLabels']) !!};
            var categoriesValues = {!! json_encode($expenses['last30daysCostPerGroupValues']) !!};

            var vendorsLabels = {!! json_encode($expenses['last30daysCostPerVendorLabels']) !!};
            var vendorsValues = {!! json_encode($expenses['last30daysCostPerVendorValues']) !!};
            
            var datos = {!! json_encode($expenses['data']) !!};

            var datos7dias = {!! json_encode($expenses['laste7days']) !!};
            
            datos.forEach(function(value, index) {
                nameproducts.push(value.datos.name_product);
            });
            datos.forEach(function(value, index) {
                cantidadproducts.push(value.datos.cantidad);
            });

            datos7dias.forEach(function(value, index) {
                namedias.push(value.datos.day);
                //console.log(value.datos.day);
            });
            datos7dias.forEach(function(value, index) {
                totalventas7dias.push(value.datos.total_ordenes);
            });
        </script>

        <div id="g3"> </div>
        <div class="row mt-5">
            <div class="col-xl-6">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">{{ __('Ranking de los últimos') }} ( 30 {{ __('days') }} )</h6>
                                <h2 class="mb-0">{{ __('Productos más Vendidos') }}</h2>
                            </div>

                            <div class="col-12">
                            
                                <form action="{{route('home')}}#g3" method="GET">
                                    <div class="row mt-5 input-daterange datepicker">
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Mostrar</label>
                                                <div class="input-group">
                                                    <select name="fcat" class="form-control form-control-sm noselecttwo">
                                                        <option value="1" <?php if(isset($_GET['fcat']) && $_GET['fcat']==1){echo "selected";} ?> >Items</option>
                                                        <option value="2" <?php if(isset($_GET['fcat']) && $_GET['fcat']==2){echo "selected";} ?> >Categorias</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Filtrar por mes</label>
                                                <div class="input-group">
                                                    <select name="fmes" class="form-control form-control-sm noselecttwo">
                                                        <option value="0">Ultimos 30 dias</option>
                                                        <?php 
                                                            $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                                                            for($i=1;$i<13;$i++){
                                                                $selc = "";
                                                                if(isset($_GET['fmes']) && $_GET['fmes']==$i){ $selc= "selected"; } 
                                                                echo '<option value="'.$i.'" '.$selc.'>'.$meses[$i-1].'</option>';
                                                            }
                                                        
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label"></label>
                                                <div class="input-group">
                                                    <button type="submit" class="btn btn-primary btn-sm" style="margin-top: 8px;">Filtrar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                    <div class="card-body">

                        
                        <!-- Chart -->
                        @if(count($salesValue)>0)
                            <div class="chart">
                                <canvas id="chart-bycategory" class="chart-canvas"></canvas>
                            </div>
                        @else
                            <p>{{ __('No expenses right now!') }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">{{ __('Ranking de ventas de los últimos') }} ( 7 {{ __('days') }} )</h6>
                                <h2 class="mb-0">{{ __('Ventas por Días de la Semana') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Chart -->
                        @if(count($salesValue)>0)
                            <div class="chart">
                                <canvas id="chart-byvendor" class="chart-canvas"></canvas>
                            </div>
                        @else
                            <p>{{ __('No expenses right now!') }}</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>
        @endif


        @if(auth()->user()->hasRole('owner'))
        <div id="g4"> </div>
        <div class="row mt-5">
            <div class="col-xl-12">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Mesas más Ocupadas</h6>
                                <h2 class="mb-0">Ranking de Ocupación de mesas</h2>
                            </div>
                            <div class="col-12">
                            
                            <form action="{{route('home')}}#g4" method="GET">
                                <div class="row mt-5 input-daterange datepicker">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Area</label>
                                            <div class="">
                                                <select name="tarea" class="form-control form-control-sm">
                                                    @foreach($misMesas as $key)
                                                    <option value="{{$key->id}}"  <?php if(isset($_GET['tarea']) && $_GET['tarea']==$key->id){ echo "selected"; } ?> >{{$key->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Fecha de</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input name="tinicio" class="form-control form-control" placeholder="Fecha de" type="text" <?php if(isset($_GET['tinicio'])){echo 'value="'.$_GET['tinicio'].'"';} ?>/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Fecha hasta</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input name="tfin" class="form-control form-control" placeholder="Fecha hasta" type="text" <?php if(isset($_GET['tfin'])){echo 'value="'.$_GET['tfin'].'"';} ?> />
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label"></label>
                                            <div class="input-group">
                                                <button type="submit" class="btn btn-primary btn" style="margin-top: 8px;">Filtrar</button>
                                            </div>
                                        </div>

                                        
                                    </div>
                                <div>
                            </form>
                                
                                
                            </div>
                        </div>
                    </div>

                    <script>
                        var tablesLables = @json($tablesLabels);
                        var tablesPeoples= @json($tablesPeoples);
                        
                        
                        totalTablesLabels=[];
                        totalTablesPeoples=[];
                        
                        for (const key in tablesLables) {
                            totalTablesLabels.push(tablesLables[key]);
                            totalTablesPeoples.push(tablesPeoples[key]);
                        }
                    </script>
                    
                    <div class="card-body">
                        @if(isset($mesaMasCaliente[0]->nomt))
                    <span class="badge badge-primary badge-pill">La mesa mas caliente es <b>{{$mesaMasCaliente[0]->nomt }}</b> con {{$mesaMasCaliente[0]->nump }} numero de personas</span>
                       @endif
                    <!-- Chart -->
                        @if(count($tablesLabels)>0)
                            <div class="chart">
                                <canvas id="chart-tables" class="chart-canvas"></canvas>
                            </div>
                        @else
                            <p>{{ __('No hay ventas en este momento!') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif


        @if(auth()->user()->hasRole('owner'))
        <div id="g5"> </div>
        <div class="row mt-5">
            <div class="col-xl-12">
                <div class="card bg-gradient-default shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-uppercase text-light ls-1 mb-1">Tiempos por pedido</h6>
                                <h2 class="mb-0 text-white">Tiempo promedio por pedidos</h2>
                            </div>
                            <div class="col-12">
                            
                                

                            <form action="{{route('home')}}#g5" method="GET">
                                <div class="row mt-5 input-daterange datepicker">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Fecha de</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input name="pinicio" class="form-control form-control" placeholder="Fecha de" type="text" <?php if(isset($_GET['pinicio'])){echo 'value="'.$_GET['pinicio'].'"';} ?> />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Fecha hasta</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input name="pfin" class="form-control form-control" placeholder="Fecha hasta" type="text" <?php if(isset($_GET['pfin'])){echo 'value="'.$_GET['pfin'].'"';} ?>/>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label"></label>
                                            <div class="input-group">
                                                <button type="submit" class="btn btn-primary btn" style="margin-top: 8px;">Filtrar</button>
                                                @if ($parameters)
                                                <a href="{{Request::fullUrl().'&report=true' }}" class="btn btn-md btn-success" style="margin-top: 8px;margin-left: 10px;" >{{ __('Download report') }}</a>
                                                @else
                                                <a href="{{Request::fullUrl().'?report=true' }}" class="btn btn-md btn-success" style="margin-top: 8px;margin-left: 10px;" >{{ __('Download report') }}</a>
                                                @endif
                                                
                                            </div>
                                        </div>

                                        
                                    </div>
                                <div>
                            </form>
                                
                                
                            </div>
                        </div>
                    </div>

                    <script>
                        var periodLabels = @json($periodLabels);
                        var periodTime= @json($periodTime);
                        
                        
                        totalPeriodLabels=[];
                        totalPeriodTime=[];
                        
                        for (const key in periodLabels) {
                            totalPeriodLabels.push(periodLabels[key]);
                        }
                        for (const key in periodTime) {
                            totalPeriodTime.push(periodTime[key]);
                        }

                    </script>
                    
                    <div class="card-body">
                        @if(isset($mesaMasCaliente->nomt))
                        @endif
                        <!-- Chart -->
                        @if(count($tablesLabels)>0)
                            <div class="chart">
                                <canvas id="chart-timeorder" class="chart-canvas"></canvas>
                            </div>
                        @else
                            <p>{{ __('No hay ventas en este momento!') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif




        @if(auth()->user()->hasRole('owner'))
        <div id="g6"> </div>
        <div class="row mt-5">
            <div class="col-xl-12">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Informe por Horario</h6>
                                <h2 class="mb-0">Ordenes por horario</h2>
                            </div>
                            <div class="col-12">
                            
                                

                            <form action="{{route('home')}}#g6" method="GET">
                                <div class="row mt-5 input-daterange datepicker">

                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Fecha de</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input name="hinicio" class="form-control form-control" placeholder="Fecha de" type="text" <?php if(isset($_GET['hinicio'])){echo 'value="'.$_GET['hinicio'].'"';} ?> />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Fecha hasta</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input name="hfin" class="form-control form-control" placeholder="Fecha hasta" type="text" <?php if(isset($_GET['hfin'])){echo 'value="'.$_GET['hfin'].'"';} ?>/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Hora de</label>
                                            <div class="">

                                                <select name="hhde" class="form-control" required>
                                                    <?php 
                                                        $k=0;
                                                        for($i=1;$i<25;$i++){
                                                            $selected = "";

                                                             $k++;  if($k==13){$k=1;}  
                                                           

                                                            $form = "AM"; if($i>=12 && $i<24){$form="PM";}
                                                            if(isset($_GET['hhde']) && $_GET['hhde']==$i){
                                                                $selected = "selected";
                                                            }
                                                            if(!isset($_GET['hhde']) && $i==7){
                                                                $selected = "selected";
                                                            }
                                                            echo '<option value="'.$i.'" '.$selected.' >'.$k.':00 '.$form.'</option>';
                                                        }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Hora hasta</label>
                                            <div class="">

                                                <select name="hhha" class="form-control" required>
                                                    <?php 
                                                        $k=0;
                                                        for($i=1;$i<25;$i++){
                                                            $selected = "";

                                                             $k++;  if($k==13){$k=1;}  
                                                           

                                                            $form = "AM"; if($i>=12 && $i<24){$form="PM";}
                                                            if(isset($_GET['hhha']) && $_GET['hhha']==$i){
                                                                $selected = "selected";
                                                            }
                                                            if(!isset($_GET['hhha']) && $i==19){
                                                                $selected = "selected";
                                                            }
                                                            echo '<option value="'.$i.'" '.$selected.' >'.$k.':00 '.$form.'</option>';
                                                        }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label class="form-control-label"></label>
                                            <div class="input-group">
                                                <button type="submit" class="btn btn-primary btn" style="margin-top: 8px;">Filtrar</button>
                                                @if ($parameters)
                                                <a href="{{Request::fullUrl().'&reportweekofday=true' }}" class="btn btn-md btn-success" style="margin-top: 8px;margin-left: 10px;" >{{ __('Download report') }}</a>
                                                @else
                                                <a href="{{Request::fullUrl().'?reportweekofday=true' }}" class="btn btn-md btn-success" style="margin-top: 8px;margin-left: 10px;" >{{ __('Download report') }}</a>
                                                @endif
                                                
                                            </div>
                                        </div>

                                        
                                    </div>
                                <div>
                            </form>
                                
                                
                            </div>
                        </div>
                    </div>

                    <script>
                        var horarioLabels = @json($horarioLabels);
                        var horarioOrders= @json($horarioOrders);
                        
                        
                        totalhorarioLabels=[];
                        totalhorarioOrders=[];
                        
                        for (const key in horarioLabels) {
                            totalhorarioLabels.push(horarioLabels[key]);
                        }
                        for (const key in horarioOrders) {
                            totalhorarioOrders.push(horarioOrders[key]);
                        }

                    </script>
                    
                    <div class="card-body">
                        @if(isset($mesaMasCaliente->nomt))
                        @endif
                        <!-- Chart -->
                        @if(count($tablesLabels)>0)
                            <div class="chart">
                                <canvas id="chart-hourorder" class="chart-canvas"></canvas>
                            </div>
                        @else
                            <p>{{ __('No hay ventas en este momento!') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif


        @if(auth()->user()->hasRole('owner'))
        <div id="g7"> </div>
        <div class="row mt-5">
            <div class="col-xl-12">
                <div class="card bg-gradient-default shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-uppercase text-light ls-1 mb-1">Informe por Dias</h6>
                                <h2 class="mb-0 text-white">Cantidad de ventas por día</h2>
                            </div>
                            <div class="col-12">
                            
                                

                            <form action="{{route('home')}}#g7" method="GET">
                                <div class="row mt-5 input-daterange datepicker">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Mesero</label>
                                            <div class="">
                                                <select name="mmes" class="form-control form-control-sm">
                                                    <option value="0"  >Seleccionar mesero</option>
                                                    @foreach($misMeseros as $key)
                                                    <option value="{{$key->id}}" <?php if(isset($_GET['mmes']) && $_GET['mmes']==$key->id){ echo "selected"; } ?>  >{{$key->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Fecha de</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input name="minicio"   class="form-control form-control" placeholder="Fecha de" type="text" <?php if(isset($_GET['minicio'])){echo 'value="'.$_GET['minicio'].'"';} ?> />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Fecha hasta</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                </div>
                                                <input name="mfin" class="form-control form-control" placeholder="Fecha hasta" type="text" <?php if(isset($_GET['mfin'])){echo 'value="'.$_GET['mfin'].'"';} ?>/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label"></label>
                                            <div class="input-group">
                                                <button type="submit" class="btn btn-primary btn" style="margin-top: 8px;">Filtrar</button>
                                               
                                            </div>
                                        </div>

                                        
                                    </div>
                                <div>
                            </form>
                                
                                
                            </div>
                        </div>
                    </div>

                    <script>
                        var ordenespordiaLabels = @json($ordenespordiaLabels);
                        var ordenespordiaValues= @json($ordenespordiaValues);
                        
                        
                        totalorderbydayLabels=[];
                        totalorderbydayValues=[];
                        
                        for (const key in ordenespordiaLabels) {
                            totalorderbydayLabels.push(ordenespordiaLabels[key]);
                            totalorderbydayValues.push(ordenespordiaValues[key]);
                        }

                    </script>
                    
                    <div class="card-body">
                        <!-- Chart -->
                        @if(count($ordenespordiaLabels)>0)
                            <div class="chart">
                                <canvas id="chart-orderbyday" class="chart-canvas"></canvas>
                            </div>
                        @else
                            <p>{{ __('No hay ventas en este momento!') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif



        @if(auth()->user()->hasRole('owner'))
        <div id="g7"> </div>
        <div class="row mt-5">
            <div class="col-xl-12">
                <div class="card shadow">
                    <div class="card-header bg-transparent">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h6 class="text-uppercase text-muted ls-1 mb-1">Informe por ventas</h6>
                                <h2 class="mb-0">Ventas por día</h2>
                            </div>
                            <div class="col-12">
                            
                                

                                <form action="{{route('home')}}#g7" method="GET">
                                    <div class="row mt-5 input-daterange datepicker">
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Mesero</label>
                                                <div class="">
                                                    <select name="vmes" class="form-control form-control-sm">
                                                        <option value="0"  >Seleccionar mesero</option>
                                                        @foreach($misMeseros as $key)
                                                        <option value="{{$key->id}}" <?php if(isset($_GET['mmes']) && $_GET['mmes']==$key->id){ echo "selected"; } ?>  >{{$key->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Mostrar</label>
                                                <div class="">
                                                    <select name="vmos" class="form-control form-control-sm">
                                                        <option value="0"  >Seleccionar</option>
                                                        <option value="1"  >Total venta</option>
                                                        <option value="2"  >Total propina</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Metodo de pago</label>
                                                <div class="">
                                                    <select name="vmos" class="form-control form-control-sm">
                                                        <option value="0"  >Seleccionar</option>
                                                        <option value="cod"  >Contraentrega</option>
                                                        <option value="cash"  >Efectivo</option>
                                                        <option value="cardterminal"  >Datafono</option>
                                                        <option value="transferencia"  >transferencia</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Tipo de pedido</label>
                                                <div class="">
                                                    <select name="vmos" class="form-control form-control-sm">
                                                        <option value="0" >Seleccionar</option>
                                                        <option value="3" >En la mesa</option>
                                                        <option value="1" >Domicilio</option>
                                                        <option value="4" >Digituno</option>
                                                        <option value="2" >Recogida</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Fecha de</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                    </div>
                                                    <input name="vinicio"   class="form-control form-control" placeholder="Fecha de" type="text" <?php if(isset($_GET['minicio'])){echo 'value="'.$_GET['minicio'].'"';} ?> />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <div class="form-group">
                                                <label class="form-control-label">Fecha hasta</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                    </div>
                                                    <input name="vfin" class="form-control form-control" placeholder="Fecha hasta" type="text" <?php if(isset($_GET['mfin'])){echo 'value="'.$_GET['mfin'].'"';} ?>/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label"></label>
                                                <div class="input-group">
                                                    <button type="submit" class="btn btn-primary btn" style="margin-top: 8px;">Filtrar</button>
                                                    @if ($parameters)
                                                    <a href="{{Request::fullUrl().'&reportbyday=true' }}" class="btn btn-md btn-success" style="margin-top: 8px;margin-left: 10px;" >{{ __('Download report') }}</a>
                                                    @else
                                                    <a href="{{Request::fullUrl().'?reportbyday=true' }}" class="btn btn-md btn-success" style="margin-top: 8px;margin-left: 10px;" >{{ __('Download report') }}</a>
                                                    @endif
                                                </div>
                                            </div>
    
                                            
                                        </div>
                                    <div>
                                </form>
                                
                            </div>
                        </div>
                    </div>

                    <script>
                        var ordenestotalpordiaLabels = @json($ordenestotalpordiaLabels);
                        var ordenestotalpordiaValues= @json($ordenestotalpordiaValues);
                        
                        
                        totalpordiaLabels=[];
                        totalpordiaValues=[];
                        
                        for (const key in ordenestotalpordiaLabels) {
                            totalpordiaLabels.push(ordenestotalpordiaLabels[key]);
                            totalpordiaValues.push(ordenestotalpordiaValues[key]);
                        }

                    </script>
                    
                    <div class="card-body">
                        <!-- Chart -->
                        @if(count($ordenespordiaLabels)>0)
                            <div class="chart">
                                <canvas id="chart-ordertotalbyday" class="chart-canvas"></canvas>
                            </div>
                        @else
                            <p>{{ __('No hay ventas en este momento!') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- @if(auth()->user()->hasRole('owner')&&config('settings.enable_pricing'))
            <br /><br />
            @include("plans.info",['planAttribute'=> auth()->user()->restorant->getPlanAttribute(),'showLinkToPlans'=>true])
        @endif --}}
        
        @include('layouts.footers.auth')
    </div>
    @endif
@endsection
@section('topjs')
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ asset('argon') }}/vendor/chart.js/dist/Chart.extension.js"></script>
@endsection
@push('js')
  

      

@if(
    (auth()->user()->hasRole('admin')&&config('app.isft')) ||
    (auth()->user()->hasRole('owner')&&in_array("drivers", config('global.modules',[]))) 
)

    <!-- Live orders -->
    <script src="{{ asset('custom') }}/js/liveorders.js"></script>

    <!-- Google Map -->
    <script async defer src= "https://maps.googleapis.com/maps/api/js?libraries=geometry,drawing&callback=initDriverMap&key=<?php echo config('settings.google_maps_api_key'); ?>"> </script>
      
    <script type="text/javascript">
    var map=null;
    var clientsAndDriverMarkers=[];

    function initDriverMap(){
        map = new google.maps.Map(document.getElementById('map_location'), {center: {lat: 40.7128, lng: -74.006}, zoom: 15 });
        getRestorants();
    }

    function getRestorants(){

        var infowindow = new google.maps.InfoWindow(); 

        const image ="/custom/img/pin_restaurant.svg";

        var bounds = new google.maps.LatLngBounds();

        var link='/restaurantslocations';
        axios.get(link).then(function (response) {
            

            response.data.restaurants.forEach(restaurant => {

                    /**
                     *  Restaurant Marker
                     **/
                     var restoMarker=new google.maps.Marker({
                        position: new google.maps.LatLng(parseFloat(restaurant.lat), parseFloat(restaurant.lng)),
                        animation: google.maps.Animation.DROP,
                        map,
                        title: restaurant.name,
                        icon:image,
                        color:"red"
                    });

                    restoMarker.addListener("click", () => {
                        var content="<a href=\"/orders?restorant_id="+restaurant.id+"\"><strong>"+restaurant.name+"</strong></a>";
                        infowindow.setContent(content);
                        infowindow.open({
                            anchor: restoMarker,
                            map,
                            shouldFocus: false,
                        });
                    });
                    bounds.extend(restoMarker.position);
            });

            map.fitBounds(bounds);

            getDriverOrders();
            setInterval(() => {
                getDriverOrders();
            }, 20000);
            
        });
    }
   

    function getDriverOrders(){
           
            var infowindow = new google.maps.InfoWindow(); 

            const image ="/custom/img/pin_driver.svg";

            var link='/driverlocations';

           

            for (let i = 0; i < clientsAndDriverMarkers.length; i++) {
                    clientsAndDriverMarkers[i].setMap(null);
                }
                clientsAndDriverMarkers=[];
            

            axios.get(link).then(function (response) {
                

                
                response.data.drivers.forEach(driver => {
                    
                    
                    if(driver.lat!=null){

                        
                         /**
                     *  Driver Marker
                     **/
                    var driverMarker=new google.maps.Marker({
                        position: new google.maps.LatLng(parseFloat(driver.lat), parseFloat(driver.lng)),
                        map,
                        title: driver.name,
                        icon:image,
                        color:"red"
                    });
                    clientsAndDriverMarkers.push(driverMarker);
                    google.maps.event.addListener(driverMarker, 'click', (function(driverMarker, i) {
                        var content="<a href=\"/orders?driver_id="+driver.id+"\">"+driver.name+"</a>";
                        content+="<br />";
                        content+="Orders: "+driver.driverorders.length;
                        content+="<br />";
                        content+="---------";
                        content+="<br />";
                        driver.driverorders.forEach(order => {
                            content+="Order <a href=\"/orders/"+order.id+"\">#"+order.id+"</a> <a href=\"/orders?restorant_id="+order.restorant_id+"\"><strong>"+order.restorant.name+"</strong></a>";
                            content+="<br />";
                        });
                        content+="---------";
                        content+="<br />";
                        return function() {
                            infowindow.setContent(content);
                            infowindow.open(map, driverMarker);
                        }
                    })(driverMarker, i));
                    

                    /**
                     *  Driver Path
                     **/
                    var driverPathCoordinates=[];
                    driver.paths.forEach(path => {
                        driverPathCoordinates.push({lat: parseFloat(path.lat), lng: parseFloat(path.lng)});
                    });
                    driverPathCoordinates.push({lat: parseFloat(driver.lat), lng: parseFloat(driver.lng)});

                    const driverPath = new google.maps.Polyline({
                        path: driverPathCoordinates,
                        geodesic: true,
                        strokeColor: "#0000FF",
                        strokeOpacity: 1.0,
                        strokeWeight: 2,
                    });
                    driverPath.setMap(map);

                    

                    /**
                     *  Driver orders - if any
                     * */
                     driver.driverorders.forEach(order => {


                        //The restaurant
                        var restaurantMarker=new google.maps.Marker({
                            position: new google.maps.LatLng(parseFloat(order.restorant.lat), parseFloat(order.restorant.lng)),
                            title: order.restorant.name,
                            color:"red"
                        });
                        bounds.extend(restaurantMarker.position);

                        //The Client
                        var clientMarker=new google.maps.Marker({
                            position: new google.maps.LatLng(parseFloat(order.address.lat), parseFloat(order.address.lng)),
                            title: order.address.address,
                            map,
                            icon:"/custom/img/pin_client.svg",
                            color:"red"
                        });
                        bounds.extend(clientMarker.position);
                        clientsAndDriverMarkers.push(clientMarker);

                        google.maps.event.addListener(clientMarker, 'click', (function(clientMarker, i) {
                            var content="Order <a href=\"/orders/"+order.id+"\">#"+order.id+"</a> <a href=\"/orders?restorant_id="+order.restorant_id+"\"><strong>"+order.restorant.name+"</strong></a>";
                            content+="<br />Address <a href=\"/orders?client_id="+order.client_id+"\"><strong>"+order.address.address+"</strong></a>";
                               
                            return function() {
                                infowindow.setContent(content);
                                infowindow.open(map, clientMarker);
                            }
                        })(clientMarker, i));


                        var driverPathToClientCoordinates=[];

                        //Create new paths, to indicate, from driver, to restaurant if order is not picked up
                        if(order.laststatus[0].pivot.status_id<6){
                            
                            //Only if this order is not yet picked up
                            var driverPathToRestaurantCoordinates=[];
                            
                            driverPathToRestaurantCoordinates.push({lat: parseFloat(driver.lat), lng: parseFloat(driver.lng)});
                            driverPathToRestaurantCoordinates.push({lat: parseFloat(order.restorant.lat), lng: parseFloat(order.restorant.lng)});
                            driverPathToClientCoordinates.push({lat: parseFloat(order.restorant.lat), lng: parseFloat(order.restorant.lng)});

                            const driverPathToResto = new google.maps.Polyline({
                                path: driverPathToRestaurantCoordinates,
                                geodesic: true,
                                strokeColor: "#FF6000",
                                strokeOpacity: 1.0,
                                strokeWeight: 2,
                            });
                            driverPathToResto.setMap(map);
                        }else{
                            driverPathToClientCoordinates.push({lat: parseFloat(driver.lat), lng: parseFloat(driver.lng)});
                        }

                       
                            
                           //Complete path to client
                            driverPathToClientCoordinates.push({lat: parseFloat(order.address.lat), lng: parseFloat(order.address.lng)});
   
                           const driverPathToClient = new google.maps.Polyline({
                               path: driverPathToClientCoordinates,
                               geodesic: true,
                               strokeColor: "#FF6000",
                               strokeOpacity: 1.0,
                               strokeWeight: 2,
                           });
                           driverPathToClient.setMap(map);
                        });

                    }

                   
                         
                        

                   


                    
                });

              
                

                
                
            })
            .catch(function (error) {
                
            });
    };
   
    </script>
    @endif
@endpush
