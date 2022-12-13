
<div class="header pb-8 pt-5 pt-lg-8 d-flex align-items-center" style="{{ 'background-image: url('.asset('default/PQRS.png').'); background-size: cover; background-position: center top;' }}">
    <!-- Mask -->
    <span class="mask bg-gradient-default opacity-8"></span>
    <!-- Header container -->
    <div class="container-fluid d-flex align-items-center">
        <div class="row">
            <div class="col-md-12 {{ $class ?? '' }}">
                <h1 class="display-2 text-white mt-2"><i class="fas fa-info-circle text-white" aria-hidden="true" ></i> Centro de Ayuda </h1>
                @if (Auth::guest())
                    <P class="text-white mt-0 mb-5">EN CUMPLIMIENTO DE LO ESTABLECIDO EN LA LEY 1581 DE 2012 –LEY DE PROTECCIÓN DE DATOS PERSONALES Y SUS DECRETOS REGLAMENTARIOS, 
                    AL MOMENTO DE ENVIAR LA PRESENTE SOLICITUD USTED AUTORIZA Y ACEPTA QUE <strong>ACODRÉS</strong>  HAGA TRATAMIENTO DE SUS DATOS PERSONALES CONFORME A 
                    LAS POLÍTICAS QUE LA ENTIDAD TIENE PARA ESOS EFECTOS.</P>
                @endif
            </div>
        </div>
    </div>
</div>
