@extends('layouts.app', ['title' => "Reservaciones"])

@section('content')
    @include('drivers.partials.header', ['title' => "Crear reservación"])

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">Crear reservación</h3>
                            </div>
                            <div class="col-4 text-right">
                                <a href="{{ route('clients.index') }}" class="btn btn-sm btn-primary">{{ __('Back to list') }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                            <div class="pl-lg-4">
                                <form method="post" action="" autocomplete="off">
                                    @csrf
                                    @method('put')

                                </form>
                                </div>


                                <hr />
                                <h6 class="heading-small text-muted mb-4">{{ __('Client information') }}</h6>
                            <div class="pl-lg-4">


                                <div class="form-group{{ $errors->has('name_client') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="name_client">{{ __('Client') }}</label>
                                    <div class="">
                                        <select name="mmes" class="form-control form-control-sm">
                                            <option value=""  >Seleccionar cliente</option>
                                            @foreach($clients as $key)
                                            <option value="{{$key->id}}" <?php if(isset($_GET['mmes']) && $_GET['mmes']==$key->id){ echo "selected"; } ?>  >{{$key->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('name_client') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="name_client">Mesas a reservar</label>
                                    <select name="zonas[]" class="form-control" id="zonas" multiple>
                                        <option value="">Seleccionar mesas</option>
                                        <?php
                                        $k=0;
                                            foreach ($restoareas as $item){
                                                $opciones="";
                                                foreach ($restomesas as $item2){
                                                    if($item->id==$item2->restoarea_id){
                                                        $opciones.='<option value="'.$item2->id.'">'.$item2->name.'</option>';
                                                    }
                                                }
                                                echo '<optgroup label="'.$item->name.'" >'.$opciones.'</optgroup>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group{{ $errors->has('email_client') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="email_client">Motivo de reservación</label>
                                    <div class="">
                                        <select name="mmes" class="form-control form-control-sm">
                                            <option value=""  >Seleccionar cliente</option>
                                            @foreach($motive as $key)
                                            <option value="{{$key->id}}" >{{$key->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('phone_client') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="phone_client">Comentario</label>
                                    <textarea class="form-control"></textarea>
                                </div>

                                <div class="form-group{{ $errors->has('email_client') ? ' has-danger' : '' }}">
                               
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Fecha</label>
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="ni ni-calendar-grid-58"></i></span>
                                                    </div>
                                                    <input name="fromDate" class="form-control datepicker" placeholder="Fecha de" type="text">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label">Hora</label>
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
                                    </div>
                              
                                </div>

                                <div class="form-group{{ $errors->has('email_client') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="email_client">Total a pagar</label>
                                    <div class="">
                                        <p class="h1">312.321,00&nbsp;COP </p>
                                    </div>
                                </div>


                            </div>
                    </div>
                    <div class="card-footer py-4">
                        <nav class="d-flex justify-content-end" aria-label="...">
                          
                            <a type="submit" class="btn btn-md btn-primary float-left" data-toggle="modal" data-target="#modal-payment-reservation" >Guardar Cambios</a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        @include('reservation.admin.includes.modals')

        @section('js')
        <script>
            $("#paymentType").on('change', function() {
                alert();
                if ($(this).val()=='transferencia') {
                    $('#selecuenta').show()
                    $('#loadarchivo').show()
                    $('#seletipocuenta').hide()
                    $('.selecuenta2').hide()
                }else if ($(this).val()=='cardterminal') {
                    $('#selecuenta').hide()
                    $('#seletipocuenta').hide()
                    $('#loadarchivo').hide()
                    $('.selecuenta2').show()
                }else {
                    $('#selecuenta').hide()
                    $('#seletipocuenta').hide()
                    $('#loadarchivo').hide()
                    $('.selecuenta2').hide()
                }
            });
        </script>
        @endsection


        @include('layouts.footers.auth')

        
    </div>
@endsection


