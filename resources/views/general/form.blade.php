@extends('general.index', $setup)
@section('cardbody')
<form action="{{ $setup['action'] }}" method="POST" enctype="multipart/form-data">
        @csrf
        @isset($setup['isupdate'])
            @method('PUT')
        @endisset
        @isset($setup['inrow'])
            <div class="row">
        @endisset
            @include('partials.fields',['fiedls'=>$fields])
            @if (Request::route()->getName() == 'staff.create')
                <div class="col-md-6">
                    <div class="form-group" id="new_address_checkout_holder">
                        <label class="form-control-label" for="new_address_checkout">Rol</label>
                        <select class=" form-control" id="new_address_checkout" name="rol">
                            @if (isset($setup['roles']))
                                @foreach ($setup['roles'] as $item)
                                    <option value="{{$item->name}}">{{$item->name == 'staff' ? 'Mesero' : 'Cocina'}}</option>
                                @endforeach
                                
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