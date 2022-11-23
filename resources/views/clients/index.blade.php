@extends('layouts.app', ['title' => __('Clients')])

@section('content')
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
    </div>

    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col">
                <div class="card shadow">
                    <div class="card-header border-0">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Clients') }}</h3>
                            </div>
                            @if(auth()->user()->hasRole('owner'))
                            <div class="col-4 text-right">
                                <a type="button" href="{{ route('clients.index') }}?report=true" class="btnFormClient btn btn-sm btn-success" style="color:#ffffff" ><i class="fas fa-download"></i> {{ __('Download report') }}</a>

                                <button type="button" onclick="setTimeout(()=>{initPhone('phone');}, 1000);" class="btnFormClient btn btn-sm btn-primary" data-toggle="modal" data-target="#modalRegister">Agregar Cliente</button>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12">
                        @include('partials.flash')
                    </div>

                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th scope="col">{{ __('Name') }}</th>
                                    <th scope="col">{{ __('Owner') }}</th>
                                    <th scope="col">{{ __('Owner email') }}</th>
                                    <th scope="col">{{ __('Creation Date') }}</th>
                                    @if(config('settings.enable_birth_date_on_register'))
                                        <th scope="col">{{ __('Birth Date') }}</th>
                                    @endif
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $client)
                                    <tr>
                                        <td><a href="{{ route('clients.edit', $client) }}">{{ $client->name }}</a></td>
                                        <td>{{ $client->name }}</td>
                                        <td>
                                            <a href="mailto:{{ $client->email }}">{{ $client->email }}</a>
                                        </td>
                                        <td>{{ $client->created_at->format(config('settings.datetime_display_format')) }}</td>
                                        @if(config('settings.enable_birth_date_on_register'))
                                        <th scope="col">{{ $client->birth_date }}</th>
                                    @endif
                                        <td class="text-right">
                                            @if(auth()->user()->hasRole('admin'))
                                            <div class="dropdown">

                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">

                                                        <form action="{{ route('clients.destroy', $client) }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="button" class="dropdown-item" onclick="confirm('{{ __("Are you sure you want to deactivate this user?") }}') ? this.parentElement.submit() : ''">
                                                                {{ __('Deactivate') }}
                                                            </button>
                                                        </form>

                                                </div>
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-4">
                        <nav class="d-flex justify-content-end" aria-label="...">
                            {{ $clients->links() }}
                        </nav>
                    </div>
                </div>
            </div>
        </div>

<!-- Modal -->
<div class="modal fade" id="modalRegister" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form class="modal-content" id="from-create-client">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Registrar Cliente</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" >
        <div class="form-group" >
          <div class="input-group input-group-alternative mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text2"><i class="ni ni-hat-3"></i></span>
            </div>
            <input class="form-control" style="border: 0px;" placeholder="Nombre" type="text" name="name" value="" required="" autofocus="">
          </div>
        </div>
        <div class="form-group" >
          <div class="input-group input-group-alternative mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text2"><i class="far fa-id-badge"></i></span>
            </div>
            <input class="form-control" style="border: 0px;" id="fromDocCleint" minlength="7" maxlength="10" placeholder="Número de Identificación" type="text" name="number_identification" value="" required="" autofocus="">
          </div>
        </div>
        <div class="form-group" >
          <div class="input-group input-group-alternative mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text2"><i class="ni ni-email-83"></i></span>
            </div>
            <input class="form-control"  style="border: 0px;"  placeholder="Correo electrónico" type="email" name="email" value="" required="">
          </div>
        </div>
        <div class="form-group" >
          <div class="input-group input-group-alternative mb-3">
            <input class="form-control" id="formPhone"  placeholder="Teléfono" type="phone" name="phone" value="" required="">
          </div>
        </div>
      </div>
      <div class="modal-footer">
      <button type="subtmin" class="btn btn-primary">CREAR</button>
      </div>
    </form>
  </div>
</div>
        @include('layouts.footers.auth')
    </div>
@endsection
