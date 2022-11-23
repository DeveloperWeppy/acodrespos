@isset($separator)
    <br />
    <h4 id="sep{{ $id }}" class="display-4 mb-0">{{ __($separator) }}</h4>
    {{-- <hr /> --}}
@endisset
<div id="form-group-{{ $id }}" class="form-group{{ $errors->has($id) ? ' has-danger' : '' }}  @isset($class) {{$class}} @endisset">
     @if(!(isset($type)&&$type=="hidden"))
        <label class="form-control-label" for="{{ $id }}">{{ __($name) }}@isset($link)<a target="_blank" href="{{$link}}">{{$linkName}}</a>@endisset</label>
    @endif
    @if (isset($type) == 'password')
        <div class="input-group" id="show_hide_password">
            <input @isset($accept) accept="{{ $accept }}" @endisset step="{{ isset($step)?$step:".01"}}" @isset($min) min="{{ $min }}" @endisset  @isset($max) max="{{ $max }}" @endisset type="{{ isset($type)?$type:"text"}}" name="{{ $id }}" id="{{ $id }}" class="form-control @isset($editclass) {{$editclass}} @endisset  {{ $errors->has($id) ? ' is-invalid' : '' }}" placeholder="{{ __($placeholder) }}" value="{{ old($id)?old($id):(isset($value)?$value:(app('request')->input($id)?app('request')->input($id):null)) }}" <?php if($required) {echo 'required';} ?> >
            <div class="input-group-addon">
                <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
            </div>
        </div>
        @else
        <input @isset($accept) accept="{{ $accept }}" @endisset step="{{ isset($step)?$step:".01"}}" @isset($min) min="{{ $min }}" @endisset  @isset($max) max="{{ $max }}" @endisset type="{{ isset($type)?$type:"text"}}" name="{{ $id }}" id="{{ $id }}" class="form-control @isset($editclass) {{$editclass}} @endisset  {{ $errors->has($id) ? ' is-invalid' : '' }}" placeholder="{{ __($placeholder) }}" value="{{ old($id)?old($id):(isset($value)?$value:(app('request')->input($id)?app('request')->input($id):null)) }}" <?php if($required) {echo 'required';} ?> >
    @endif
    
    @isset($additionalInfo)
        <small class="text-muted"><strong>{{ __($additionalInfo) }}</strong></small>
    @endisset
    @if ($errors->has($id))
        <span class="invalid-feedback" role="alert">
            <strong>{{ $errors->first($id) }}</strong>
        </span>
    @endif
</div>
