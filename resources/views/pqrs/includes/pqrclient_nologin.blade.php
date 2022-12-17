<form enctype="multipart/form-data" autocomplete="off" id="register_pqr2">
    @csrf
    <h6 class="heading-small text-muted mb-4">{{ __('REGISTRAR SOLICITUD') }}</h6>
    <p>Con el fin de dar oportuna respuesta a su solicitud es importante que completes los campos que están con <strong>(*)</strong> </p>
    <div class="pl-lg-4">
        
        <div class="form-group">
            <label class="form-control-label" for="input-name">{{ __('Nombres y Apellidos') }}(*)</label>
            <input type="text" name="name" id="input-name" class="form-control form-control-alternative" value="{{Auth::guest()?"":auth()->user()->name}}" required autofocus>
        </div>
        <div class="form-group">
            <label class="form-control-label" for="input-email">{{ __('Email') }}(*)</label>
            <input type="email" name="email" id="input-email" class="form-control form-control-alternative" value="{{Auth::guest()?"":auth()->user()->email}}" required>
        </div>

        <div class="form-group">
            <label class="form-control-label" for="input-phone">{{ __('Phone') }}(*)</label>
            <input type="text" name="phone1" id="telephone1" class="form-control form-control-alternative" value="{{Auth::guest()?"":auth()->user()->phone}}" required>
        </div>

        <div class="form-group">
            <label class="form-control-label" for="select-option">{{ __('Seleccione opción de su PQRSD') }}(*)</label>
            <select name="type_radicate" id="select-option" class="form-control form-control-alternative noselecttwo">
                <option value="" disabled></option>
                <option value="Petición">Petición</option>
                <option value="Queja">Queja</option>
                <option value="Reclamo">Reclamo</option>
                <option value="Solicitud de Información">Solicitud de Información</option>
            </select>
        </div>

        @if (Auth::guest())
            <div class="form-group">
                <label class="form-control-label" for="num_order">{{ __('N° de Factura') }}(opcional)</label>
                <p><small>Si su PQRS está relacionado con una orden en especifico, indique el número de la orden.</small></p>
                <input type="text" name="num_order" id="num_order" class="form-control form-control-alternative">
            </div>
        @endif

        <div class="form-group">
            <label class="form-control-label" for="exampleFormControlTextarea1">Objeto de su PQRSD(*)</label>
            <textarea class="form-control form-control-alternative" name="message" id="exampleFormControlTextarea1" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label class="form-control-label" for="input-file">{{ __('Subir evidencia') }}(opcional)</label>
            <p><small>Si su PQRS está relacionado con una orden en especifico, indique el número de la orden.</small></p>
            <input type="file" name="evidence" id="input-file" class="form-control form-control-alternative" onchange="return validarExtensionArchivo()">
        </div>

        <div class="form-group text-center">
            <div class="form-check"><input type="checkbox" name="accept" id="termsCheckBox" class="h-4 w-4 border border-gray-300 rounded-sm bg-white checked:bg-blue-600 checked:border-blue-600 focus:outline-none transition duration-200 mt-1 align-top  "> 
                <label for="terms" class="form-check-label text-gray-500">
                &nbsp;&nbsp;{{__('Al hacer clic el botón enviar, usted acepta la remisión de la PQRS a Acodrés. Sus datos serán recolectados y tratados conforme con la ')}} <strong>Política de 
                    Tratamiento de Datos</strong>
                </label>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
        </div>
    </div>
</form>