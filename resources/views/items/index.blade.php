@extends('layouts.app', ['title' => __('Restaurant Menu Management')])
@section('admin_title')
    {{__('Menu')}}
@endsection
@section('content')
    @include('items.partials.modals', ['restorant_id' => $restorant_id])

    <style>


            
        .wdp-ribbon{
            display: inline-block;
            padding: 2px 15px;
            position: absolute;
            right: 0px;
            top: 20px;
            line-height: 24px;
            height:24px;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25em;
            border-radius: 0;
            text-shadow: none;
            font-weight: normal;
            background-color: #e6750b !important;
            z-index: 2;
            color: #ffffff;
        }

        
        </style>
        
    
    <div class="header bg-gradient-primary pb-7 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
            <div class="row align-items-center py-4">
                <!--<div class="col-lg-6 col-7">
                </div>-->
                <div class="col-lg-12 col-12 text-right">

                    <a href="{{ route('items.index')}}?report=true" class="btn btn-sm btn-success"><i class="fas fa-download"></i>  Descargar Informe</a>


                    @if (isset($hasMenuPDf)&&$hasMenuPDf)
                        <a target="_blank" href="{{ route('menupdf.download')}}" class="btn btn-sm btn-danger"><i class="fas fa-file-pdf"></i> {{ __('PDF Menu') }}</a>
                    @endif
                    <button class="btn btn-icon btn-1 btn-sm btn-info" type="button" data-toggle="modal" data-target="#modal-items-area-cocina" data-toggle="tooltip" data-placement="top" title="{{ __('Añadir área de cocina')}}">
                        <span class="btn-inner--icon"><i class="fa fa-plus"></i> {{ __('Añadir área de cocina') }}</span>
                    </button>
                    <button class="btn btn-icon btn-1 btn-sm btn-info" type="button" data-toggle="modal" data-target="#modal-items-category" data-toggle="tooltip" data-placement="top" title="{{ __('Add new category')}}">
                        <span class="btn-inner--icon"><i class="fa fa-plus"></i> {{ __('Add new category') }}</span>
                    </button>
                    {{-- @if($canAdd)
                    <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#modal-import-items" onClick=(setRestaurantId({{ $restorant_id }}))>
                        <span class="btn-inner--icon"><i class="fa fa-file-excel"></i> {{ __('Import from CSV') }}</span>
                    </button>
                    @endif --}}
                    @if(config('settings.enable_miltilanguage_menus'))
                        @include('items.partials.languages')
                    @endif
                </div>
            </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt--7">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col">
                                        <h3 class="mb-0">{{ __('Restaurant Menu Management') }} @if(config('settings.enable_miltilanguage_menus')) ({{ $currentLanguage}}) @endif</h3>
                                    </div>
                                    <div class="col-auto">
                                        <!--<button class="btn btn-icon btn-1 btn-sm btn-primary" type="button" data-toggle="modal" data-target="#modal-items-category" data-toggle="tooltip" data-placement="top" title="{{ __('Add new category')}}">
                                            <span class="btn-inner--icon"><i class="fa fa-plus"></i> {{ __('Add new category') }}</span>
                                        </button>
                                        @if($canAdd)
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-import-items" onClick=(setRestaurantId({{ $restorant_id }}))>
                                                <span class="btn-inner--icon"><i class="fa fa-file-excel"></i> {{ __('Import from CSV') }}</span>
                                            </button>
                                        @endif
                                        @if(config('settings.enable_miltilanguage_menus'))
                                            @include('items.partials.languages')
                                        @endif-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="col-12">
                        @include('partials.flash')
                    </div>
                    <div class="card-body">
                        @if(count($categories)==0)
                            <div class="col-lg-3" >
                                <a  data-toggle="modal" data-target="#modal-items-category" data-toggle="tooltip" data-placement="top" title="{{ __('Add new category')}}">
                                    <div class="card">
                                        <img class="card-img-top" src="{{ asset('images') }}/default/add_new_item.jpg" alt="...">
                                        <div class="card-body">
                                            <h3 class="card-title text-primary text-uppercase">{{ __('Add first category') }}</h3> 
                                        </div>
                                    </div>
                                </a>
                                <br />
                            </div>
                        @endif


                       
                        @if(isset($categories[0]->aitemsFeatured) && count($categories[0]->aitemsFeatured)>0)
                            <div class="alert alert-default" id="categoria-{{str_replace(' ', '-', "Destacados")}}">
                                <div class="row">
                                    <div class="col">
                                        <span class="h1 font-weight-bold mb-0 text-white">Destacados</span>
                                    </div>
                                </div>
                            </div>
                        
                            <div class="row justify-content-center">
                                <div class="col-lg-12">
                                    <div class="row row-grid">
                                        @foreach ($categories as $index => $category)
                                        @foreach ( $category->itemsFeatured as $item)
                                            <?php 
                                                $dsc = $restorant->applyDiscount($item->discount_id,$item->price);
                                                $textDesc = 100-number_format((($item->price-$dsc)*100)/$item->price,0);
                                            ?>
                                            <div class="col-lg-3">
                                                <a href="{{ route('items.edit', $item) }}">
                                                    <div class="card containerItem">
                                                        @if ($dsc>0 && $dsc!=null)
                                                            @if(isset($item->variants) && $item->variants->count()>0)
                                                                <span class="wdp-ribbon wdp-ribbon-three">Dto %</span>
                                                            @else
                                                                <span class="wdp-ribbon wdp-ribbon-three">{{$textDesc}}%</span>
                                                            @endif
                                                        @endif
                                                        <img class="card-img-top" src="{{ $item->logom }}" alt="...">
                                                        <div class="card-body">
                                                            <h3 class="card-title text-primary text-uppercase">{{ $item->name }}</h3>
                                                            <p class="card-text description mt-3">{{ $item->description }}</p>
                                                            @if ($dsc>0 && $dsc!=null)
                                                                <span class="text-muted" style="text-decoration: line-through;">@money($item->price, config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                                                            @endif
                                                            <span class="badge badge-primary badge-pill">@money($item->price-$dsc, config('settings.cashier_currency'),config('settings.do_convertion'))</span>

                                                            <p class="mt-3 mb-0 text-sm">
                                                                @if($item->available == 1)
                                                                <span class="text-success mr-2">{{ __("AVAILABLE") }}</span>
                                                                @else
                                                                <span class="text-danger mr-2">{{ __("UNAVAILABLE") }}</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <br/>
                                                </a>
                                            </div>
                                        @endforeach
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                   
                        

                        {{--***********************************************--}}
                       



                        @foreach ($categories as $index => $category)
                        @if($category->active == 1)
                        <div class="alert alert-default" id="categoria-{{str_replace(' ', '-', $category->name)}}">
                            <div class="row">
                                <div class="col">
                                    @php
                                       $vname="";
                                       $vcolor="";
                                        if(isset($category->areakitchen)){
                                            $valor= $category->areakitchen;
                                            $vname=$valor->name;
                                            $vcolor=$valor->colorarea;
                                        }
                                       
                                    @endphp
                                    <span class="h1 font-weight-bold mb-0 text-white">{{ $category->name }}</span>
                                    <span class="mb-0 text-white " style=" display: inline-block; margin-left: 10px; padding: .25rem .5rem; border-radius: 8px; background-color:  {{$vcolor}}">{{$vname}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="row">
                                        <script>
                                            function setSelectedCategoryId(id){
                                                $('#category_id').val(id);
                                            }

                                            function setRestaurantId(id){
                                                $('#res_id').val(id);
                                            }

                                        </script>
                                        @if($canAdd)
                                            <button class="btn btn-icon btn-1 btn-sm btn-primary" type="button" data-toggle="modal" data-target="#modal-new-item" data-toggle="tooltip" data-placement="top" title="{{ __('Add item') }} in {{$category->name}}" onClick=(setSelectedCategoryId({{ $category->id }})) >
                                                <span class="btn-inner--icon"><i class="fa fa-plus"></i></span>
                                            </button>
                                        @else
                                            <a href="{{ route('plans.current')}}" class="btn btn-icon btn-1 btn-sm btn-warning" type="button"  >
                                                <span class="btn-inner--icon"><i class="fa fa-plus"></i> {{ __('Menu size limit reaced') }}</span>
                                            </a>
                                        @endif
                                        <button class="btn btn-icon btn-1 btn-sm btn-warning" type="button" id="edit" data-toggle="modal" data-target="#modal-edit-category" data-toggle="tooltip" data-placement="top" title="{{ __('Edit category') }} {{ $category->name }}" 
                                            data-id="<?= $category->id ?>" data-name="<?= $category->name ?>" >
                                            <span class="btn-inner--icon"><i class="fa fa-edit"></i></span>
                                        </button>

                                       

                                        <form action="{{ route('categories.destroy', $category) }}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button class="btn btn-icon btn-1 btn-sm btn-danger" type="button" onclick="confirm('{{ __("Are you sure you want to delete this category?") }}') ? this.parentElement.submit() : ''" data-toggle="tooltip" data-placement="top" title="{{ __('Delete') }} {{$category->name}}">
                                                <span class="btn-inner--icon"><i class="fa fa-trash"></i></span>
                                            </button>
                                        </form>

                                        @if(count($categories)>1)
                                            <div style="margin-left: 10px; margin-right: 10px">|</div>
                                        @endif

                                         <!-- UP -->
                                         @if ($index!=0)
                                            <a href="{{ route('items.reorder',['up'=>$category->id]) }}"  class="btn btn-icon btn-1 btn-sm btn-success" >
                                                <span class="btn-inner--icon"><i class="fas fa-arrow-up"></i></span>
                                            </a>
                                         @endif
                                         

                                        <!-- DOWN -->
                                        @if ($index+1!=count($categories))
                                            <a href="{{ route('items.reorder',['up'=>$categories[$index+1]->id]) }}" class="btn btn-icon btn-1 btn-sm btn-success">
                                                <span class="btn-inner--icon"><i class="fas fa-arrow-down"></i></span>
                                            </a>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($category->active == 1)
                            <div class="row justify-content-center">
                                <div class="col-lg-12">
                                    <div class="row row-grid">
                                        @foreach ( $category->items as $item)
                                            @if($item->has_featured==0)
                                            <?php 
                                                $dsc = $restorant->applyDiscount($item->discount_id,$item->price);
                                                $textDesc = 100-number_format((($item->price-$dsc)*100)/$item->price,0);
                                            ?>
                                            <div class="col-lg-3">
                                                <a href="{{ route('items.edit', $item) }}">
                                                    <div class="card containerItem">
                                                        @if ($dsc>0 && $dsc!=null)
                                                            @if(isset($item->variants) && $item->variants->count()>0)
                                                                <span class="wdp-ribbon wdp-ribbon-three">Dto %</span>
                                                            @else
                                                                <span class="wdp-ribbon wdp-ribbon-three">{{$textDesc}}%</span>
                                                            @endif
                                                        @endif
                                                        <img class="card-img-top" src="{{ $item->logom }}" alt="...">
                                                        <div class="card-body">
                                                            <h3 class="card-title text-primary text-uppercase">{{ $item->name }}</h3>
                                                            <p class="card-text description mt-3">{{ $item->description }}</p>
                                                            @if ($dsc>0 && $dsc!=null)
                                                                <span class="text-muted" style="text-decoration: line-through;">@money($item->price, config('settings.cashier_currency'),config('settings.do_convertion'))</span>
                                                            @endif
                                                            <span class="badge badge-primary badge-pill">@money($item->price-$dsc, config('settings.cashier_currency'),config('settings.do_convertion'))</span>

                                                            <p class="mt-3 mb-0 text-sm">
                                                                @if($item->available == 1)
                                                                <span class="text-success mr-2">{{ __("AVAILABLE") }}</span>
                                                                @else
                                                                <span class="text-danger mr-2">{{ __("UNAVAILABLE") }}</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <br/>
                                                </a>
                                            </div>
                                            @endif
                                        @endforeach
                                        @if($canAdd)
                                        <div class="col-lg-3" >
                                            <a   data-toggle="modal" data-target="#modal-new-item" data-toggle="tooltip" data-placement="top" href="javascript:void(0);" onclick=(setSelectedCategoryId({{ $category->id }}))>
                                                <div class="card">
                                                    <img class="card-img-top" src="{{ asset('images') }}/default/add_new_item.jpg" alt="...">
                                                    <div class="card-body">
                                                        <h3 class="card-title text-primary text-uppercase">{{ __('Add item') }}</h3>
                                                    </div>
                                                </div>
                                            </a>
                                            <br />
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    //reemplazar %20 en la url por -
    if (location.hash)
        location.replace(location.hash.replace(/\+|%20/g, "-"));

    //Asignar evento cuando se cambie el hash
    window.addEventListener("hashchange", function(event){
        if (location.hash)
                location.replace(location.hash.replace(/\+|%20/g, "-"));

            //Descomentar para mostrar en consola cuando se dispara
            //console.debug("Se disparó el evento hashchange: ", event.oldURL, "-->", event.newURL);
        });
  $("[data-target='#modal-edit-category']").on('click',function() {
    var id = $(this).attr('data-id');
    var name = $(this).attr('data-name');


    
    $('#cat_name').val(name);
    $("#form-edit-category").attr("action", "/categories/"+id);
})



</script>


@endsection