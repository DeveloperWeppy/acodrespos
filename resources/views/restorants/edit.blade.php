@extends('layouts.app', ['title' => __('Orders')])

@section('content')
<div class="header bg-gradient-info pb-6 pt-5 pt-md-8">
    <div class="container-fluid">

        <div class="nav-wrapper">
            <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="res_menagment" role="tablist">

                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 active " id="tabs-menagment-main" data-toggle="tab" href="#menagment" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-badge mr-2"></i>{{ __('Restaurant Management')}}</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-menagment-main" data-toggle="tab" href="#accountbanks" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="fas fa-money-check"></i> {{ __('Cuentas Bancarias')}}</a>
                </li>

                @if(count($appFields)>0)
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-menagment-main" data-toggle="tab" href="#apps" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-spaceship mr-2"></i>{{ __('Apps')}}</a>
                    </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0" id="tabs-menagment-main" data-toggle="tab" href="#hours" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-time-alarm mr-2"></i>{{ __('Working Hours')}}</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link mb-sm-3 mb-md-0 " id="tabs-menagment-main" data-toggle="tab" href="#location" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-square-pin mr-2"></i>{{ __('Location')}}</a>
                </li>
                
                @if(auth()->user()->hasRole('admin'))
                    <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0" id="tabs-menagment-main" data-toggle="tab" href="#plan" role="tab" aria-controls="tabs-menagment" aria-selected="true"><i class="ni ni-money-coins mr-2"></i>{{ __('Plans')}}</a>
                    </li>
                @endif
            </ul>
        </div>

    </div>
</div>



<div class="container-fluid mt--7">
    <div class="row">
        <div class="col-12">
            <br />

            @include('partials.flash')

            <div class="tab-content" id="tabs">


                <!-- Tab Managment -->
                <div class="tab-pane fade show active" id="menagment" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Restaurant Management') }}</h3>
                                    @if (config('settings.wildcard_domain_ready'))
                                    <span class="blockquote-footer">{{ $restorant->getLinkAttribute() }}</span>
                                    @endif
                                </div>
                                <div class="col-4 text-right">
                                    @if(auth()->user()->hasRole('admin'))
                                    <a href="{{ route('admin.restaurants.index') }}"
                                        class="btn btn-sm btn-info">{{ __('Back to list') }}</a>
                                    @endif
                                    @if (!config('settings.is_pos_cloud_mode')&&!config('app.issd'))
                                        @if (config('settings.wildcard_domain_ready'))
                                        <a target="_blank" href="{{ $restorant->getLinkAttribute() }}"
                                            class="btn btn-sm btn-success">{{ __('View it') }}</a>
                                        @else
                                        <a target="_blank" href="{{ route('vendor',$restorant->subdomain) }}"
                                            class="btn btn-sm btn-success">{{ __('View it') }}</a>
                                        @endif
                                       
                                    @endif
                                    @if ($hasCloner)
                                        <a href="{{ route('admin.restaurants.create')."?cloneWith=".$restorant->id }}" class="btn btn-sm btn-warning text-white">{{ __('Clone it') }}</a>
                                    @endif
                                        

                                </div>

                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="heading-small text-muted mb-4">{{ __('Restaurant information') }}</h6>
                            
                            @include('restorants.partials.info')
                            <hr />
                            @include('restorants.partials.owner')
                        </div>
                    </div>
                </div>

                <!---  tabs de configuración de cuentas --->
                <div class="tab-pane fade " id="accountbanks" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">{{ __('Configuración de Datos de Cuentas Bancarias') }}</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-register-account">
                                        Agregar Cuenta
                                    </button>
                                    @include('restorants.partials.configaccountbanks')
                                </div>

                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="heading-small text-muted mb-4">{{ __('Datos de Cuentas') }}</h6>

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table align-items-center table-flush">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('Nombre Banco') }}</th>
                                                <th scope="col">{{ __('Tipo de Cuenta') }}</th>
                                                <th scope="col">{{ __('Nº Cuenta') }}</th>
                                                <th scope="col">{{ __('Eliminar') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (count($config_account_banks)>0)
                                                @foreach ($config_account_banks as $account)
                                                    <tr>
                                                        <td>{{$account->name_bank}}</td>
                                                        <td>{{$account->type_account}}</td>
                                                        <td>{{$account->number_account}}</td>
                                                        <td>
                                                            <input type="hidden" id="{{$account->id}}" value="{{ route('configuracioncuenta.delete', $account->id) }}">
                                                            <button class="btn btn-danger btn-sm" onclick="eliminarAccount({{$account->id}})"><span class="btn-inner--icon"><i class="ni ni-fat-remove"></i></span> {{ __('crud.delete') }}</button>    
                                                        </td> 
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td>No hay datos disponibles...</td>
                                                </tr>
                                            @endif
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Apps -->
                @if(count($appFields)>0)
                    <div class="tab-pane fade show" id="apps" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                        @include('restorants.partials.apps') 
                    </div>
                @endif

                <!-- Tab Location -->
                <div class="tab-pane fade show" id="location" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    @include('restorants.partials.location')
                </div>
                

                <!-- Tab Hours -->
                <div class="tab-pane fade show" id="hours" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    @include('restorants.partials.hours')
                </div>

                <!-- Tab Hours -->
                @if(auth()->user()->hasRole('admin'))
                    <div class="tab-pane fade show" id="plan" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                        @include('restorants.partials.plan')
                    </div>
                @endif

            </div>
        </div>
    </div>
    @include('layouts.footers.auth')
</div>
@endsection

@section('js')
<!-- Google Map -->
<script async defer src= "https://maps.googleapis.com/maps/api/js?libraries=geometry,drawing&key=<?php echo config('settings.google_maps_api_key'); ?>"> </script>

    <script type="text/javascript">
        "use strict";
        var defaultHourFrom = "09:00";
        var defaultHourTo = "17:00";
        var datazone=[];
        var timeFormat = '{{ config('settings.time_format') }}';
        $('.input-group-addon').hide();
        $("#show_hide_password").removeClass("input-group");

        function formatAMPM(date) {
            var hours = date.split(':')[0];
            var minutes = date.split(':')[1];

            var ampm = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
           
            var strTime = hours + ':' + minutes + ' ' + ampm;
            return strTime;
        }



        var config = {
            enableTime: true,
            dateFormat: timeFormat == "AM/PM" ? "h:i K": "H:i",
            noCalendar: true,
            altFormat: timeFormat == "AM/PM" ? "h:i K" : "H:i",
            altInput: true,
            allowInput: true,
            time_24hr: timeFormat == "AM/PM" ? false : true,
            onChange: [
                function(selectedDates, dateStr, instance){
                    //...
                    this._selDateStr = dateStr;
                },
            ],
            onClose: [
                function(selDates, dateStr, instance){
                    if (this.config.allowInput && this._input.value && this._input.value !== this._selDateStr) {
                        this.setDate(this.altInput.value, false);
                    }
                }
            ]
        };

        $("input[type='checkbox'][name='days']").change(function() {
            var hourFrom = flatpickr($('#'+ this.value + '_from'+"_shift"+$('#'+ this.id).attr("valuetwo")), config);
            var hourTo = flatpickr($('#'+ this.value + '_to'+"_shift"+$('#'+ this.id).attr("valuetwo")), config);

            if(this.checked){
                hourFrom.setDate(timeFormat == "AP/PM" ? formatAMPM(defaultHourFrom) : defaultHourFrom, false);
                hourTo.setDate(timeFormat == "AP/PM" ? formatAMPM(defaultHourTo) : defaultHourTo, false);
            }else{
                hourFrom.clear();
                hourTo.clear();
            }
        });

        $('input:radio[name="primer"]').change(function(){
            if($(this).val() == 'map') {
                $("#clear_area").hide();
            }else if($(this).val() == 'area' && isClosed){
                $("#clear_area").show();
            }
        });

        $("#clear_area").on("click",function() {
            //borra 1
            //remove markers
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }

            //remove polygon
            poly.setMap(null);
            poly.setPath([]);
           
            poly = new google.maps.Polyline({ map: map_area, path: [], strokeColor: "#FF0000", strokeOpacity: 1.0, strokeWeight: 2 });

            path = poly.getPath();

            //update delivery path
            changeDeliveryArea(getLatLngFromPoly(path))

            isClosed = false;
            $("#clear_area").hide();
        });

        //Initialize working hours
        function initializeWorkingHours(){
            var shifts = {!! json_encode($shifts) !!};
            
            if(shifts != null){
                Object.keys(shifts).map((shiftKey)=>{
                    var sk=shiftKey;
                    var workingHours=shifts[shiftKey];
                    
                    Object.keys(workingHours).map((key, index)=>{
                        //now we have the shifts
                        if(workingHours[key] != null){
                            
                            var hour = flatpickr($('#'+key+'_shift'+shiftKey), config);
                            hour.setDate(workingHours[key], false);

                            var day_key = key.split('_')[0];
                            $('#day'+day_key+'_shift'+shiftKey).attr('checked', 'checked');
                        }
                    });
                    

                })
            }
        }

        function getLocation(callback){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type:'GET',
                url: '/get/rlocation/'+$('#rid').val(),
                success:function(response){
                    if(response.status){
                        return callback(true, response.data)
                    }
                }, error: function (response) {
                return callback(false, response.responseJSON.errMsg);
                }
            })
        }

        function changeLocation(lat, lng){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type:'POST',
                url: '/updateres/location/'+$('#rid').val(),
                dataType: 'json',
                data: {
                    lat: lat,
                    lng: lng
                },
                success:function(response){
                    if(response.status){
                        
                    }
                }
            })
        }

        function changeDeliveryArea(path){
            var mensaje="La zona ha sido borrada";
            if(path.length>0){
                mensaje="La Zona de entrega se a guardado"; 
            }
            Swal.fire({
                icon: 'warning',
                title: 'Validan los datos',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
            }
            );
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type:'POST',
                url: '/updateres/delivery/'+$('#rid').val(),
                dataType: 'json',
                headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                data: {
                    path: JSON.stringify(path)
                },
                success:function(response){
                    if(response.status){
                        Swal.fire({
                        icon: 'success',
                        title: mensaje,
                        showConfirmButton: false,
                        timer: 1500
                        });
                    }
                }
            })
        }

        function initializeMap(lat, lng){
            // en 1
            infoWindow = new google.maps.InfoWindow();
            const locationButton = document.createElement("button");
            var map_options = {
                zoom: 13,
                center: new google.maps.LatLng(lat, lng),
                mapTypeId: "terrain",
                scaleControl: true,
                styles:[
                  {
                    "featureType": "administrative.land_parcel",
                    "stylers": [
                      {
                        "visibility": "off"
                      }
                    ]
                  },
                  {
                    "featureType": "administrative.neighborhood",
                    "stylers": [
                      {
                        "visibility": "off"
                      }
                    ]
                  },
                  {
                    "featureType": "poi",
                    "elementType": "labels.icon",
                    "stylers": [
                      {
                        "visibility": "off"
                      }
                    ]
                  },
                  {
                    "featureType": "poi",
                    "elementType": "labels.text",
                    "stylers": [
                      {
                        "visibility": "off"
                      }
                    ]
                  },
                  {
                    "featureType": "road",
                    "elementType": "labels",
                    "stylers": [
                      {
                        "visibility": "on"
                      }
                    ]
                  },
                  {
                    "featureType": "water",
                    "elementType": "labels.text",
                    "stylers": [
                      {
                        "visibility": "off"
                      }
                    ]
                  }
                ]
            }

            map_location = new google.maps.Map( document.getElementById("map_location"), map_options );

            locationButton.innerHTML="<i class='fas fa-map-marked-alt'></i>";
            locationButton.classList.add("custom-map-control-button");
            map_location.controls[google.maps.ControlPosition.RIGHT_CENTER].push(locationButton);
            locationButton.addEventListener("click", () => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        infoWindow.setPosition(pos);
                        map_location.setCenter(pos);
                        marker.setPosition(new google.maps.LatLng(pos.lat, pos.lng));
                        changeLocation(pos.lat, pos.lng);
                        },
                        () => {
                        handleLocationError(true, infoWindow, map_location.getCenter());
                        }
                    );
                } else {
                    handleLocationError(false, infoWindow, map_location.getCenter());
                }
            });

            map_area = new google.maps.Map( document.getElementById("map_area"), map_options );
            map_area2 = new google.maps.Map( document.getElementById("map_area2"), map_options );
            arraypoly[0]=new google.maps.Polyline({strokeColor: "blue",strokeOpacity: 1.0,strokeWeight: 3});
            arraypoly[0].setMap(map_area2)
           // poly2 = new google.maps.Polyline({strokeColor: "blue",strokeOpacity: 1.0,strokeWeight: 3});
            //poly2.setMap(map_area2);
            map_area2.addListener('click', function(event) {
                addLatLng(event.latLng);
            });
            for(var i = 0; i < datazone.length; i++) {
                initialize_existing_area2(datazone[i].radius,i,datazone[i].color);
            }
            indexpoly=datazone.length;
            arraypoly[indexpoly] =  new google.maps.Polyline({strokeColor: "blue",strokeOpacity: 1.0,strokeWeight: 3});
            arraypoly[indexpoly].setMap(map_area2);
            cargarzona();
        }
        var markIni=-1;
        var arrayLocatio2=[];
        function addLatLng(latLng,simular=false) {
            if (isClosed2) return;
            markerIndex2 = arraypoly[indexpoly].getPath().length;
            var ifcrear=0;
            if(markerIndex2==0){
                $( "#btnerase" ).show();
            }
            var icona=area;
            arrayLocatio.push(latLng);
            if(arraypoly[indexpoly].getPath().length==0){
                icona=start;
            }else{
                if(arrayLocatio.length>2){
                    markers2[markers2.length-1].setMap(null);
                    markers2[markers2.length-1]=new google.maps.Marker({ map: map_area2, position: arrayLocatio[arrayLocatio.length-2], draggable: false, icon: area });
                }
                icona="/images/blue_pin2.png";
            }
            if(markIni==-1){
                markIni=markers2.length;
            }
           // markerIndex2 = poly2.getPath().length;
            var isFirstMarker = markerIndex2 === 0;
            markerArea2 = new google.maps.Marker({ map: map_area2, position: latLng, draggable: false, icon: icona });

            if(icona=="/images/blue_pin2.png"){
                markerArea2.addListener("rightclick", () => {
                    borrapositionmark();
                });
            }
            if(arrayLocatio.length==1){
                markerArea2.addListener("rightclick", () => {
                    $( "#btnerase" ).click();
                });
            }
            markers2.push(markerArea2);

            if(isFirstMarker) {
                google.maps.event.addListener(markerArea2, 'click', function () {
                    if (isClosed2) return;
                    markers2[markIni].setIcon(area);
                    markers2[markers2.length-1].setIcon(area);
                    path2f = arraypoly[indexpoly].getPath();
                    arraypoly[indexpoly].setMap(null);
                    arrayLocatio=[];
                    //al crear 2 encerr
                    arraypoly[indexpoly] = new google.maps.Polygon({ map: map_area2, path: path2f, strokeColor: "yellow", strokeOpacity: 0.8, strokeWeight: 2, fillColor: "green", fillOpacity: 0.35, editable: false });   
                    isClosed2 = true;
                    indexpoly++;
                    arraypoly[indexpoly] =  new google.maps.Polyline({strokeColor: "blue",strokeOpacity: 1.0,strokeWeight: 3});
                    arraypoly[indexpoly].setMap(map_area2);
                    $( "#btnsave" ).show();
                    modalZone();
                });
            }
            google.maps.event.addListener(markerArea2, 'drag', function (dragEvent) {
                arraypoly[indexpoly].getPath().setAt(markerIndex2, dragEvent.latLng);
            });
            if(simular){
                if (isClosed2) return;
                    markers2[markIni].setIcon(area);
                    markers2[markers2.length-1].setIcon(area);
                    path2f = arraypoly[indexpoly].getPath();
                    arraypoly[indexpoly].setMap(null);
                    arrayLocatio=[];
                    $( "#btnsave" ).show();
            }
            arrayLocatio2=arrayLocatio;
            arraypoly[indexpoly].getPath().push(latLng);
           
        }
        function initializeMarker(lat, lng){
            // en 2
          
            var markerData = new google.maps.LatLng(lat, lng);
            marker = new google.maps.Marker({
                position: markerData,
                map: map_location,
                icon: start
            });
        }
function borrarmarkply(index,tipo=0){
    for (var i = 0; i < markers2.length; i++) {
        markers2[i].setMap(null);
    }
    if(isClosed2 && tipo==1){
        index--;
    }
    arraypoly[index] .setMap(null);
    arraypoly[index] .setPath([]);
    arraypoly[index]  = new google.maps.Polyline({ map: map_area2, path: [], strokeColor:"blue", strokeOpacity: 1.0, strokeWeight: 2 });
    path2f = arraypoly[index].getPath();
    
    isClosed2 = false;
    $("#btnerase").hide();
    $( "#btnsave" ).hide();
}
function borrapositionmark2(index){
    
    if (isClosed) return;
    if (index<markers.length) return;
    var arrayp=JSON.parse(JSON.stringify(poly.getPath()));
    var keyi= Object.keys(arrayp);
    var arrayn=poly.getPath();
    arrayn=arrayn[keyi[0]];
    arrayn=arrayn.slice(0,arrayn.length-1);
    poly.setPath(arrayn);
    
    path = poly.getPath();
    markers[markers.length-1].setMap(null);
    markers=markers.slice(0,markers.length-1);
   
   
   
}
function borrapositionmark(){
    if (isClosed2) return;
   
    markers2[markers2.length-1].setMap(null);
    markers2=markers2.slice(0,markers2.length-1);
    arrayLocatio=arrayLocatio.slice(0,arrayLocatio.length-1);
    arraypoly[indexpoly].setPath(arrayLocatio);
  
    if(arrayLocatio.length>1){
        markers2[markers2.length-1].setMap(null);
        var markertemp=new google.maps.Marker({ map: map_area2, position: arrayLocatio[arrayLocatio.length-1], draggable: false, icon: "/images/blue_pin2.png" });
        markertemp.addListener("rightclick", () => {
                    borrapositionmark();
    });
    markers2[markers2.length-1]=markertemp;
    }
}
$( "#btnerase" ).click(function() {
    for(var i = markIni; i<markers2.length; i++) {
        markers2[i].setMap(null);
    }
    arrayLocatio=[];
    var indexpt=indexpoly;
    if(isClosed2){
        indexpt--;
    }
    arraypoly[indexpt] .setMap(null);
    arraypoly[indexpt] .setPath([]);
    arraypoly[indexpt]  = new google.maps.Polyline({ map: map_area2, path: [], strokeColor: "blue", strokeOpacity: 1.0, strokeWeight: 2 });
    path2f = arraypoly[indexpt].getPath();
    isClosed2 = false;
    $("#btnerase").hide();
    $( "#btnsave" ).hide();
});
        function getLatLngFromPoly(path){
            //borra 2
            //get lat long from polygon
            var polygonBounds = path;
            var bounds = [];
            for (var i = 0; i < polygonBounds.length; i++) {
                var point = {
                    lat: polygonBounds.getAt(i).lat(),
                    lng: polygonBounds.getAt(i).lng()
                };

                bounds.push(point);
            }

            return bounds;
        }
         // if lleno solo 1 ? 2
        function new_delivery_area(latLng){
            // al crear 1
            if (isClosed) return;
            markerIndex = poly.getPath().length;
            var isFirstMarker = markerIndex === 0;
            markerArea = new google.maps.Marker({ map: map_area, position: latLng, draggable: false, icon: area });

           
            //push markers
            markers.push(markerArea);
            var pos=markers.length;
            google.maps.event.addListener(markerArea, 'rightclick', function (evt) { 
                  borrapositionmark2(pos);
            });
            if(isFirstMarker) {
                google.maps.event.addListener(markerArea, 'click', function () {
                    if (isClosed) return;
                    path = poly.getPath();
                    poly.setMap(null);
                    //al crear 2 encerr
                    poly = new google.maps.Polygon({ map: map_area, path: path, strokeColor: "#FF0000", strokeOpacity: 0.8, strokeWeight: 2, fillColor: "#FF0000", fillOpacity: 0.35, editable: false });
                    isClosed = true;

                    //update delivery path
                    changeDeliveryArea(getLatLngFromPoly(path));
                    //show button clear
                    
                });
            }
            //show button clear
            $("#clear_area").show();

            google.maps.event.addListener(markerArea, 'drag', function (dragEvent) {
                poly.getPath().setAt(markerIndex, dragEvent.latLng);
            });
            poly.getPath().push(latLng);
        }
        function initialize_existing_area2(area_positions,index,color){
            for(var i=0; i<area_positions.length; i++){
                var markerAreaData = new google.maps.LatLng(area_positions[i].lat, area_positions[i].lng);
                markerArea2 = new google.maps.Marker({ map: map_area2, position: markerAreaData, draggable: false, icon: area });
                markers2.push(markerArea2);

            }
            arraypoly[index]=new google.maps.Polygon({ map: map_area2, paths: area_positions, strokeColor: color, strokeOpacity: 0.8, strokeWeight: 2, fillColor: color, fillOpacity: 0.35, editable: false });
        }
        function initialize_existing_area(area_positions){
            // en 3

             //if basio 2
            for(var i=0; i<area_positions.length; i++){
                var markerAreaData = new google.maps.LatLng(area_positions[i].lat, area_positions[i].lng);
                markerArea = new google.maps.Marker({ map: map_area, position: markerAreaData, draggable: false, icon: area });

                //push markers
                markers.push(markerArea);

         
                path = poly.getPath();

                poly.setMap(null);
                poly = new google.maps.Polygon({ map: map_area, path: path, strokeColor: "#FF0000", strokeOpacity: 0.8, strokeWeight: 2, fillColor: "#FF0000", fillOpacity: 0.35, editable: false });
              //  poly2 = new google.maps.Polygon({ map: map_area2, path: path, strokeColor: "#FF0000", strokeOpacity: 0.8, strokeWeight: 2, fillColor: "#FF0000", fillOpacity: 0.35, editable: false });
                //show clear area
                isClosed = true;
                $("#clear_area").show();
             }


        }

        var start = "/images/pin.png"
        var area = "/images/green_pin.png"
        var map_location = null;
        var map_area = null;
        var map_area2 = null;
        var marker = null;
        var infoWindow = null;
        var lat = null;
        var lng = null;
        var circle = null;
        var isClosed = false;
        var isClosed2 = true;
        var poly = null;
        var poly2 = null;
        var arraypoly=[];
        var indexpoly=0;
        var markers = [];
        var markerArea = null;
        var markerIndex = null;
        var markerIndex2 = null;
        var markerArea2 = null;
        var markers2 = [];
        var arrayLocatio = [];
        var path = null;
        var  path2f=null;
        var  path2f2=null;
        window.onload = function () {
          

            //Working hours
            initializeWorkingHours();

            getLocation(function(isFetched, currPost){
                infoWindow = new google.maps.InfoWindow();

                if(isFetched){
                    infoWindow = new google.maps.InfoWindow;

                    if(currPost.lat != 0 && currPost.lng != 0){
                        //initialize map
                        initializeMap(currPost.lat, currPost.lng)

                        //initialize marker
                        initializeMarker(currPost.lat, currPost.lng)
                        //if basio 1
                        

                        poly = new google.maps.Polyline({ map: map_area, path: currPost.area ? currPost.area : [], strokeColor: "#FF0000", strokeOpacity: 1.0, strokeWeight: 2 });
                       

  // Add a listener for the click event
                        if(currPost.area != null){
                            initialize_existing_area(currPost.area)
                        }

                        map_location.addListener('click', function(event) {
                            marker.setPosition(new google.maps.LatLng(event.latLng.lat(), event.latLng.lng()));

                            changeLocation(event.latLng.lat(), event.latLng.lng());
                        });
                      
                        map_area.addListener('click', function(event) {
                            new_delivery_area(event.latLng)
                        });
                       
                    }else{
                        
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                var pos = { lat: position.coords.latitude, lng: position.coords.longitude };

                                //initialize map
                                initializeMap(position.coords.latitude, position.coords.longitude)

                                //initialize marker
                                initializeMarker(position.coords.latitude, position.coords.longitude)

                                //change location in database
                                changeLocation(pos.lat, pos.lng);

                                map_location.addListener('click', function(event) {
                                    marker.setPosition(new google.maps.LatLng(event.latLng.lat(), event.latLng.lng()));

                                    changeLocation(event.latLng.lat(), event.latLng.lng());
                                });
                              
                                map_area.addListener('click', function(event) {
                                    new_delivery_area(event.latLng)
                                });
                            }, function() {
                                // handleLocationError(true, infoWindow, map.getCenter());

                                //initialize map
                                initializeMap(54.5260, 15.2551)

                                //initialize marker
                                initializeMarker(54.5260, 15.2551)

                                map_location.addListener('click', function(event) {
                                    marker.setPosition(new google.maps.LatLng(event.latLng.lat(), event.latLng.lng()));

                                    changeLocation(event.latLng.lat(), event.latLng.lng());
                                });
                                
                                map_area.addListener('click', function(event) {
                                    new_delivery_area(event.latLng)
                                });
                            });
                        }
                    }
                }
            });
        }

        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ? 'Error: The Geolocation service failed.' : 'Error: Your browser doesn\'t support geolocation.');
            infoWindow.open(map_location);
        }

        /*var form = document.getElementById('restorant-form');
        form.addEventListener('submit', async function(event) {
            event.preventDefault();
            
            var address = $('#address').val();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/restaurant/address',
                dataType: 'json',
                data: { address: address},
                success:function(response){
                    if(response.status){
                        if(response.results.lat && response.results.lng){
                            initializeMap(response.results.lat, response.results.lng);
                            initializeMarker(response.results.lat, response.results.lng);
                            changeLocation(response.results.lat, response.results.lng);

                            map_location.addListener('click', function(event) {
                                marker.setPosition(new google.maps.LatLng(event.latLng.lat(), event.latLng.lng()));

                                changeLocation(event.latLng.lat(), event.latLng.lng());
                            });
                        }
                    }
                }
            })

            form.submit();
        });*/
        function eliminarAccount(id){
            
            var url = document.getElementById(id).getAttribute('value');
            //console.log("soy id"+urll);
                Swal.fire({
                    title: 'Eliminar Cuenta de Banco',
                    text: "¿Estas seguro de eliminar esta cuenta?",
                    icon: 'question',
                    showCancelButton: "Cancelar",
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Si, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            type: "GET",
                            encoding:"UTF-8",
                            url: url,
                            dataType:'json',
                            beforeSend:function(){
                                Swal.fire({
                                    text: 'Eliminando, espere...',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: () => {
                                        Swal.showLoading()
                                    },
                                });
                            }
                        }).done(function(respuesta){
                            //console.log(respuesta);
                            if (!respuesta.error) {
                                Swal.fire({
                                    title: 'Cuenta Eliminada!',
                                    icon: 'success',
                                    showConfirmButton: true,
                                    timer: 2000
                                });
                                setTimeout(function(){
                                location.reload();
                                },2000);
                            } else {
                                setTimeout(function(){
                                    Swal.fire({
                                        title: respuesta.mensaje,
                                        icon: 'error',
                                        showConfirmButton: true,
                                        timer: 4000
                                    });
                                },2000);
                            }
                        }).fail(function(resp){
                            console.log(resp);
                        });
                    }
                })
        }
     var typeFormZone=0;
     function cargarzona(){
        markIni=-1;
        var arrayLocatio3=arrayLocatio2;
        arrayLocatio=[];
        var ifcerrar=isClosed2;
        for(var i = 0; i < datazone.length; i++) {
            borrarmarkply(i);
        }
       markers2=[];

       $.ajax({method: "get", url: "{{route('geozone.get')}}", dataType: "json",data: { "_token": "{{ csrf_token() }}"} })
        .done(function( resp ) {
            $("#tgeozone").html(resp.table);
            $("#btnsave").hide();
            $("#btnerase").hide(); 
            $( "#btncancel" ).hide();
            for(var i = 0; i < arraypoly.length; i++) {
                arraypoly[i].setMap(null);
                arraypoly[i].setPath([]);
            }
            arraypoly=[];
            for(var i = 0; i < resp.data.length; i++) {
                initialize_existing_area2(resp.data[i].radius,i,resp.data[i].color);
            }
            datazone=resp.data;
            indexpoly=resp.data.length;
            arraypoly[indexpoly] =  new google.maps.Polyline({strokeColor: "blue",strokeOpacity: 1.0,strokeWeight: 3});
            arraypoly[indexpoly].setMap(map_area2);
            eventosgepzone();
            arrayLocatio2=[];
            var indexpt=indexpoly;
            if(arrayLocatio3.length>0){
                for(var i = 0; i <arrayLocatio3.length; i++) {
                    if(ifcerrar && i==(arrayLocatio3.length-1)){
                        addLatLng(arrayLocatio3[i],true); 
                    }else{
                        addLatLng(arrayLocatio3[i]); 
                    }
                }
                if(ifcerrar){
                    arraypoly[indexpoly] = new google.maps.Polygon({ map: map_area2, path: arrayLocatio3, strokeColor: "yellow", strokeOpacity: 0.8, strokeWeight: 2, fillColor: "green", fillOpacity: 0.35, editable: false });
                   
                }
            } 
        });
     }
     function modalZone(data=0){
        if(data!=0){
            typeFormZone=data[0];
            $('#fzone-name').val(data[1]);
            $('#fzone-color').val(data[2]);
            $('#fzone-status').val(data[3]);
            $('#fzone-valor').val(data[4]);
            $('#ftitle').html("Editar Área de Entrega");
            $('#btn-edit-zone').html("Modificar");

            
        }else{
            typeFormZone=0;
            $('#ftitle').html("Crear Área de Entrega");
            $('#fzone-name').val("");
            $('#fzone-color').val("");
            $('#fzone-status').val(1);
            $('#btn-edit-zone').html("Guardar");
           
        }
        $('#modal-edit-zone').modal('show');
     }
     $( "#btnsave" ).click(function() {
            modalZone();
     });
     $( "#btnaddzone" ).click(function() {
         isClosed2=false;
         
         $( "#btncancel" ).show();
       
     });
     $("#btncancel").click(function() {
         isClosed2=true;
         $( "#btnerase" ).hide();
         $( "#btncancel" ).hide();
         $( "#btnsave" ).hide();
       
     });
     
     function eventosgepzone(){
        $( ".geoedit" ).click(function() {
            modalZone(JSON.parse($(this).attr("data")));
        }); 
        $(".geodelet").on( "click", function() {
            Swal.fire({
                title: 'Deseas eliminar esta área?',
                icon: 'warning',
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: "Cancelar",
                }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({method: "get", headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},url: "{{route('geozone.destroy')}}/"+$(this).attr("data-id") })
                    .done(function( msg ) {

                        Swal.fire('Area eliminada!', '', 'success');

                        cargarzona();
                    });
                
                }
            });
        });
     }
     eventosgepzone();
     $( "#fzone" ).submit(function( event ) {
        event.preventDefault();
        var radiusform="";
        if(path2f==null && typeFormZone==0){
            Swal.fire({
                icon: 'info',
                title: 'Selecciona zona ',
                html: 'Para poder guardar.',
                timer: 1500,   
            });
            return true;
        }
        Swal.fire({
            title: 'Validando datos',
            html: 'Por favor espere.',
            timer: 2000,
            timerProgressBar: true
        });
        $('#modal-edit-zone').modal('hide');
        var urlf="{{route('geozone.store')}}";
        if(typeFormZone!=0){
            urlf="{{route('geozone.updated')}}/"+typeFormZone;
        }else{
            radiusform=JSON.stringify(path2f);
        }
        $.ajax({
            type:"POST",
            url: urlf,
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            data:{ name:$('#fzone-name').val(),radius:radiusform,price:$('#fzone-valor').val(),colorarea:$('#fzone-color').val(),active: $('#fzone-status').val()} ,
            dataType: "json",
            success: function (data) {
                 if(data.mensaje){
                    Swal.fire({
                        icon: 'success',
                        title: 'Guardado',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $("#btnsave").hide();
                    $("#btnerase").hide(); 
                    $( "#btncancel" ).hide();
                    arrayLocatio2=[];
                    eventosgepzone();
                    cargarzona();
                    path2f=null;
                    markIni=-1;
                 }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#modal-edit-zone').modal('show');
                 }
            },
            error: function (data) {
                Swal.fire({
                        icon: 'error',
                        title: 'Error Proceso',
                        showConfirmButton: false,
                        timer: 1500
                 });
               
                console.log(data);
            },
        });
    });    
    </script>
@endsection
