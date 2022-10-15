@include('restorants.partials.modals')
@include('poscloud::pos.modals')
<?php

function clean($string)
{
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
?>
<div style="display: none" class="container-fluid py-2" id="orderDetails">

    <div class="row" style="height: calc(100vh - 110px);">
        <div class="col-sm-4 d-inline-block" style="background-color:#e9ecef; height:100%; overflow:auto;">
            @include('poscloud::pos.cartSideMenu')
        </div>
        <div id="start" class="col-sm-8 d-inline-block" style="height:100%;">

            <!-- Navbar Dark -->
            <div class="mt--3  navbar-expand-lg navbar-dark bg-gradient-dark z-index-3 py-3">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-8">
                            <div class="field mt--3">

                                <select class="select2init noselecttwo" id="itemsSelect"
                                    placeholder="{{ __('Search for item') }}">
                                    <option></option>
                                    @if (!$restorant->categories->isEmpty())
                                        @foreach ($restorant->categories as $key => $category)
                                            @if (!$category->items->isEmpty())
                                                <optgroup label="{{ $category->name }}">
                                                    @foreach ($category->aitems as $item)
                                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                    @endforeach


                                                </optgroup>
                                            @endif
                                        @endforeach
                                    @endif



                                </select>

                            </div>
                        </div>
                        <div class="col-4 d-flex justify-content-end">
                            <button type="button" class="btn bg-gradient-primary" data-bs-toggle="modal"
                                data-bs-target="#modalCategories">{{ __('Categories') }}</button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- End Navbar -->

            <div class="row mt-3 px-5" style="height:90%; overflow:auto;">
                @if (!$vendor->categories->isEmpty())

                    @foreach ($vendor->categories as $key => $category)
                        @if (!$category->aitems->isEmpty())
                            <div class="mt-4"
                                id="{{ clean(str_replace(' ', '', strtolower($category->name)) . strval($key)) }}"
                                class="{{ clean(str_replace(' ', '', strtolower($category->name)) . strval($key)) }}">
                                <h1>{{ $category->name }}</h1>
                            </div>
                        @endif


                        @foreach ($category->aitems as $item)
                            <div onClick="setCurrentItem({{ $item->id }})" class="col-xl-3 col-md-6 mb-3 mt-3">
                                <div class="card">
                                    <div class="position-relative">
                                        <a class="d-block shadow-xl border-radius-xl">
                                            <img src="{{ $item->logom }}" alt="img-blur-shadow"
                                                class="img-fluid shadow border-radius-xl">
                                        </a>
                                    </div>
                                    <div class="card-body px-2 pb-1">
                                        <span class="badge bg-gradient-light">@money($item->price, config('settings.cashier_currency'), config('settings.do_convertion'))</span><br />
                                        <strong class="text-dark mb-2 text">{{ $item->name }}</strong>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @endif


            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modal-add-consumidor" tabindex="-1" role="dialog" aria-labelledby="modal-form"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-" role="document" id="">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="modalTitle" class="modal-title" id="">División de Cuentas para esta Mesa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="false">×</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="card shadow border-0">
                    <div class="card-body px-lg-5 py-lg-5">
                        <!----- pregunta si va a ser cuenta dividida --->
                        <div class="row" id="">
                            <div class="col-sm col-md col-lg col-lg" id="">
                                <div class="quantity-area">
                                    <div class="form-group">
                                        <lottie-player src="{{ asset('animations/money.json') }}"
                                            background="transparent" speed="1" style="width: 200px; height: 200px;"
                                            loop autoplay></lottie-player>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="custom-control custom-control-alternative custom-checkbox">
                                <input class="custom-control-input" name="valor" id="ask_divide_check" type="checkbox">
                                <label class="custom-control-label" for="ask_divide_check">
                                    <span class="text-muted" id="span_dividir">No dividir cuenta</span>
                                </label>
                            </div>
                        </div>

                        <div class="quantity-area text-center" id="btncontinuar">
                            <div class="form-group"></div>
                            <div class="quantity-btn ">
                                <div id="">
                                    <button type="button" class="btn btn-primary" id="btcontinuar">Continuar</button>
                                </div>
                            </div>

                        </div>

                        <!---- crear consumidor para cuenta dividida al carrito --->
                        <div class="row" id="row_names">
                            <div class="col-sm-12" id="">
                                <label for="">Ingrese la cantidad de Personas en las que se dividirá la
                                    cuenta</label>
                                <input type="number" class="form-control" placeholder="Ingrese la cantidad"
                                    id="myInput">
                            </div>

                            <!---- inputs dinámicos ----->
                            <div class="col-sm-12 mt-2" id="content"></div>

                            <div class="quantity-area text-center">
                                <div class="form-group"></div>
                                <div class="quantity-btn ">
                                    <div id="addToCart1 ">
                                        <button type="button" class="btn btn-primary"
                                            onclick="savedivisionpersons()">Añadir Comenzal</button>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
    <script src="{{ asset('custom') }}/js/order.js"></script>
    @include('restorants.phporderinterface')
    <script>

        $(function() {
            $('#modal-add-consumidor').modal({backdrop: 'static', keyboard: false});
            //localStorage.removeItem("personas");
            $('#personitem').hide();
            $("#btcontinuar").click(function() {
                $("#modal-add-consumidor").modal("hide");
            });
            $("#myInput").keydown(function() {
                var cantidad = $("#myInput").val();
                RenderInputs(cantidad);
            });

            $("#myInput").keyup(function() {
                var cantidad = $("#myInput").val();
                RenderInputs(cantidad);
            });

        });

        function RenderInputs(cantidad) {
            $('#content').html('');
            for (var i = 0; i < cantidad; i++) {
                var fondo = random_color('hex');
                $('#content').append('<div class="col-6 mt-3">');
                $('#content').append('<input class="form-control" type="text" id="input' + (i + 1) + '" value="Persona ' + (
                        i + 1) + '" name="divididos[]" placeholder="Persona' + (i + 1) + '"/>');
                /* $('#content').append('<input type="hidden" id="input' + (i + 1) + '" name="colors_divididos[]" value="' +
                    fondo + '"/>'); */
                $('#content').append('</div>');
            }
        }

        function random_color(format) {
            var rint = Math.floor(0x100000000 * Math.random());
            switch (format) {
                case 'hex':
                    return '#' + ('00000' + rint.toString(16)).slice(-6).toUpperCase();
                case 'hexa':
                    return '#' + ('0000000' + rint.toString(16)).slice(-8).toUpperCase();
                default:
                    return rint;
            }
        }

        function savedivisionpersons() {
            var nuevovalor = [];
            var table = $("#mesaid").val();
            var inps = document.getElementsByName('divididos[]');
            for (var i = 0; i < inps.length; i++) {
                var inp = inps[i];
                //alert("divididos[" + i + "].value=" + inp.value);
                // El arreglo:
                array = {
                   'nombre': inp.value,
                   'id_mesa': table,
                };
                nuevovalor.push(array);
            }
            // Se guarda en localStorage despues de JSON stringificarlo 
            localStorage.setItem(table, JSON.stringify(nuevovalor));
            $("#modal-add-consumidor").modal("hide");
            Swal.fire(
            'Cuenta Dividida Correctamente!',
            '',
            'success'
            )

        }
    </script>
@endsection
