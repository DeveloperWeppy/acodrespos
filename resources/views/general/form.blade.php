@extends('general.index', $setup)
@section('cardbody')
<form class="newItem" action="{{ $setup['action'] }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($setup['isupdate'])
            @method('PUT')
        @endisset
        @isset($setup['inrow'])
            <div class="row">
        @endisset
            @include('partials.fields',['fiedls'=>$fields])
            {{-- @if (Request::route()->getName() == 'staff.create' || Request::route()->getName() == 'staff.edit') --}}
            @if (Request::route()->getName() == 'staff.create')
                <div class="col-md-6">
                    <div class="form-group" id="new_address_checkout_holder">
                        <label class="form-control-label" for="new_address_checkout">Rol</label>
                        <select class=" form-control" id="new_address_checkout" value name="rol">
                            @if (isset($setup['roles']) )
                               @if (!isset($setup['rol']) )
                                    @foreach ($setup['roles'] as $item)
                                        <option  value="{{$item->name}}">{{$item->name == 'staff' ? 'Mesero' : ($item->name == 'manager_restorant' ? 'Administrador de Restaurante' : 'Cocina')}}</option>
                                    @endforeach
                                @else
                                    @foreach ($setup['roles'] as $item)
                                        @if (isset($setup['rol'][0])==$item->name)
                                           <option selected="selected"  value="{{$item->name}}">{{$item->name == 'staff' ? 'Mesero' : ($item->name == 'manager_restorant' ? 'Administrador de Restaurante' : 'Cocina')}}</option>
                                        @else
                                           <option  value="{{$item->name}}">{{$item->name == 'staff' ? 'Mesero' : 'Cocina'}}</option>
                                        @endif
                                    @endforeach
                                @endif
                                
                            @endif
                            
                        </select>
                    </div>
                </div>
            @endif
           
        @isset($setup['inrow'])
            </div>
        @endisset
        @if (isset($setup['isupdate']))
            <button type="submit" class="btn btn-primary">{{ __('Update')}}</button>  
        @else
            <button type="submit" class="btn btn-primary">{{ __('Insert')}}</button>  
        @endif
    </form>
@endsection