<?php

namespace App\Http\Controllers;

use DB;
use App\Coupons;
use App\Items;
use Carbon\Carbon;
use Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Discount;
use App\Models\DiscountItems;

use App\Categories;

class CouponsController extends Controller
{
    /**
     * Provide class.
     */
    private $provider = Coupons::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'admin.restaurant.coupons.';

    /**
     * View path.
     */
    private $view_path = 'coupons.';

    /**
     * Parameter name.
     */
    private $parameter_name = 'coupon';

    /**
     * Title of this crud.
     */
    private $title = 'cupón';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'cupones';

    private function getFields()
    {
        return [
            ['class'=>'col-md-4', 'ftype'=>'input', 'name'=>'Name', 'id'=>'name', 'placeholder'=>'Código del cupón', 'required'=>true],
            ['class'=>'col-md-4', 'ftype'=>'input', 'type'=>'number', 'name'=>'Code', 'id'=>'size', 'placeholder'=>'Ingrese el tamaño de la persona de la mesa, ej. 4', 'required'=>true],
            ['ftype'=>'select', 'name'=>'Price', 'id'=>'type', 'placeholder'=>'Tipo de cupón', 'data'=>['Precio Fijo', 'Porcentaje'], 'required'=>true],
            ['ftype'=>'select', 'name'=>'Active from', 'id'=>'type', 'required'=>true],
            ['ftype'=>'select', 'name'=>'Active to', 'id'=>'type', 'required'=>true],
            ['ftype'=>'select', 'name'=>'Limite de usuarios', 'id'=>'type',  'required'=>true],
            ['ftype'=>'select', 'name'=>'Usado', 'id'=>'type', 'required'=>true],
        ];
    }

    /**
     * Auth checker functin for the crud.
     */
    private function authChecker()
    {
        $this->ownerOnly();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authChecker();

        $cupones = $this->getRestaurant()->coupons()->orderBy('id','desc')->paginate(10, ['*'], 'cupones');
        $descuentos = Discount::where('companie_id',auth()->user()->restaurant_id)->orderBy('id','desc')->paginate(10, ['*'], 'descuentos');

        return view($this->view_path.'index', ['setup' => [
            'title'=>__('crud.item_managment', ['item'=>__($this->titlePlural)]),
            'action_link'=>route($this->webroute_path.'create'),
            'action_name'=>__('crud.add_new_item', ['item'=>__($this->title)]),
            'items'=>$cupones,
            'discounts'=>$descuentos,
            'item_names'=>$this->titlePlural,
            'webroute_path'=>$this->webroute_path,
            'fields'=>$this->getFields(),
            'parameter_name'=>$this->parameter_name,
        ]]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authChecker();

        $codeCupon = strtoupper(substr($this->getRestaurant()->name, 0, 2).(Str::random(6)));

        return view('coupons.createcupon',compact('codeCupon'));
    }

    public function createDiscount()
    {
        $this->authChecker();

        $productos = [];
        $categorias = Categories::where('restorant_id',auth()->user()->restaurant_id)->get();

        foreach ($categorias as $index => $category){
            foreach ( $category->items as $item){
                $item = [
                    'id'=>$item->id,
                    'name'=>$item->name,
                    'category'=>$category->name,
                    'price'=>$item->price,
                    'date-created'=>$item->created_at,
                ];
                array_push($productos, $item);
            }
        }

        return view('coupons.creatediscount',compact('productos','categorias'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authChecker();

        $item = $this->provider::create([
            'name' => $request->name,
            'code' => $request->code,
            'type' => isset($request->type) ? $request->type : 0,
            'price' => isset($request->price) ? $request->price : null,
            'active_from' => isset($request->active_from) ? $request->active_from : '',
            'active_to' =>isset($request->active_to) ? $request->active_to : '',
            'limit_to_num_uses' => $request->limit_to_num_uses,
            'redemption' => $request->red,
            'min_price_cart' => $request->min_price,
            'has_ilimited' => $request->has_ilimited=="true" ? 1: 0,
            'has_free_delivery' => $request->has_free_delivery=="true" ? 1: 0,
            'has_discount' => $request->has_discount=="true" ? 1 : 0,
            'restaurant_id' => $this->getRestaurant()->id,
            'active'=>1
        ]);

        $item->save();

        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_added', ['item'=>__($this->title)]));
        
    }

    public function storeDiscount(Request $request)
    {
        $this->authChecker();

        list($time, $ampm) = explode(' ', $request->hora1);
        list($hh1, $mm) = explode(':', $time);
        if($ampm == 'AM' && $hh1 == 12) {
            $hh1 = '00';
        } elseif($ampm == 'PM' && $hh1 < 12) {
            $hh1 += 12;
        }

        list($time, $ampm) = explode(' ', $request->hora2);
        list($hh2, $mm) = explode(':', $time);
        if($ampm == 'AM' && $hh2 == 12) {
            $hh2 = '00';
        } elseif($ampm == 'PM' && $hh2 < 12) {
            $hh2 += 12;
        }
        

        $idss = "";
        if(isset($request->prod)){
            $idss = implode(',',$request->prod);
        }
        if(isset($request->catt)){
            $idss = implode(',',$request->catt);
        }
        $item = Discount::create([
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->type == 0 ? $request->price_fixed : $request->price_percentage,
            'active_from' => $request->active_from." ".$hh1.":".$mm,
            'active_to' => $request->active_to." ".$hh2.":".$mm,
            'opcion_discount' => $request->typ2,
            'companie_id' => $this->getRestaurant()->id,
            'items_ids'=>$idss,
            'active'=>1
        ]);

        $item->save();

        if($request->typ2==0){
            $categories=auth()->user()->restorant->categories;
            foreach ($categories as $index => $category){
                foreach ( $category->items as $product){
                    $mesas = Items::updateOrCreate(
                        [
                            'id' => $product->id,
                        ],
                        [
                            'discounted_price'=>$request->type == 0 ? $request->price_fixed : $request->price_percentage,
                            'discount_id'=>$item->id,
                        ]
                    );
                }
            }
        }

        if($request->typ2==1){
            if(isset($request->prod)){
                foreach($request->prod as $key){
                    $mesas = Items::updateOrCreate(
                        [
                            'id' => $key,
                        ],
                        [
                            'discounted_price'=>$request->type == 0 ? $request->price_fixed : $request->price_percentage,
                            'discount_id'=>$item->id,
                        ]
                    );
                }
            }
        }

        if($request->typ2==1){
            if(isset($request->catt)){
                foreach($request->catt as $key){
                    $mesas = Items::updateOrCreate(
                        [
                            'category_id' => $key,
                        ],
                        [
                            'discounted_price'=>$request->type == 0 ? $request->price_fixed : $request->price_percentage,
                            'discount_id'=>$item->id,
                        ]
                    );
                }
            }
        }
        


        return 1;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Coupons  $coupons
     * @return \Illuminate\Http\Response
     */
    public function show(Coupons $coupons)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Coupons  $coupons
     * @return \Illuminate\Http\Response
     */
    public function edit(Coupons $coupon)
    {
        return view('coupons.editcupon', ['coupon' => $coupon]);
    }

    public function editDiscount(Discount $coupon)
    {
        $productos = [];
        $categorias = Categories::where('restorant_id',auth()->user()->restaurant_id)->get();

        foreach ($categorias as $index => $category){
            foreach ( $category->items as $item){
                $item = [
                    'id'=>$item->id,
                    'name'=>$item->name,
                    'category'=>$category->name,
                    'price'=>$item->price,
                    'date-created'=>$item->created_at,
                ];
                array_push($productos, $item);
            }
        }

        return view('coupons.editdiscount', compact('coupon','productos','categorias'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Coupons  $coupons
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authChecker();
       
        $item = $this->provider::updateOrCreate(
            [
                'id' => $id,
            ],
            [
                'name' => $request->name,
                'code' => $request->code,
                'type' => isset($request->type) ? $request->type : 0,
                'price' => isset($request->price) ? $request->price : null,
                'active_from' => isset($request->active_from) ? $request->active_from : '',
                'active_to' =>isset($request->active_to) ? $request->active_to : '',
                'limit_to_num_uses' => $request->limit_to_num_uses,
                'redemption' => $request->red,
                'min_price_cart' => $request->min_price,
                'has_ilimited' => $request->has_ilimited=="true" ? 1: 0,
                'has_free_delivery' => $request->has_free_delivery=="true" ? 1: 0,
                'has_discount' => $request->has_discount=="true" ? 1 : 0,
                'restaurant_id' => $this->getRestaurant()->id,
                'active'=>1
            ]);

        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_updated', ['item'=>__($this->title)]));
    }
    public function updateDiscount(Request $request, $id)
    {
        $this->authChecker();

        list($time, $ampm) = explode(' ', $request->hora1);
        list($hh1, $mm) = explode(':', $time);
        if($ampm == 'AM' && $hh1 == 12) {
            $hh1 = '00';
        } elseif($ampm == 'PM' && $hh1 < 12) {
            $hh1 += 12;
        }

        list($time, $ampm) = explode(' ', $request->hora2);
        list($hh2, $mm) = explode(':', $time);
        if($ampm == 'AM' && $hh2 == 12) {
            $hh2 = '00';
        } elseif($ampm == 'PM' && $hh2 < 12) {
            $hh2 += 12;
        }

        $idss = "";
        if(isset($request->prod)){
            $idss = implode(',',$request->prod);
        }
        if(isset($request->catt)){
            $idss = implode(',',$request->catt);
        }
        $item = Discount::updateOrCreate(
            [
                'id'=>$id
            ],
            [
            'name' => $request->name,
            'type' => $request->type,
            'price' => $request->type == 0 ? $request->price_fixed : $request->price_percentage,
            'active_from' => $request->active_from." ".$hh1.":".$mm,
            'active_to' => $request->active_to." ".$hh2.":".$mm,
            'opcion_discount' => $request->typ2,
            'items_ids'=>$idss,
            ]
        );

        $item->save();

        if($request->typ2==0){
            $categories=auth()->user()->restorant->categories;
            foreach ($categories as $index => $category){
                foreach ( $category->items as $product){
                    $mesas = Items::updateOrCreate(
                        [
                            'id' => $product->id,
                        ],
                        [
                            'discounted_price'=>$request->type == 0 ? $request->price_fixed : $request->price_percentage,
                            'discount_id'=>$item->id,
                        ]
                    );
                }
            }
        }

        if($request->typ2==1){
            if(isset($request->prod)){
                foreach($request->prod as $key){
                    $mesas = Items::updateOrCreate(
                        [
                            'id' => $key,
                        ],
                        [
                            'discounted_price'=>$request->type == 0 ? $request->price_fixed : $request->price_percentage,
                            'discount_id'=>$item->id,
                        ]
                    );
                }
            }
        }

        if($request->typ2==1){
            if(isset($request->catt)){
                foreach($request->catt as $key){
                    $mesas = Items::updateOrCreate(
                        [
                            'category_id' => $key,
                        ],
                        [
                            'discounted_price'=>$request->type == 0 ? $request->price_fixed : $request->price_percentage,
                            'discount_id'=>$item->id,
                        ]
                    );
                }
            }
        }
        


        return 1;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Coupons  $coupons
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->delete();
        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_removed', ['item'=>__($this->title)]));
    }

    public function destroyDiscount($id)
    {
        $this->authChecker();
        $item = Discount::findOrFail($id);
        $item->delete();
        return redirect()->route($this->webroute_path.'index')->withStatus(__('Descuento removido', ['item'=>__($this->title)]));
    }



    public function apply(Request $request)
    {
        $coupon = Coupons::where(['code' => $request->code])->get()->first();
        if($coupon){
            $deduct=$coupon->calculateDeduct($request->cartValue);
            if($deduct){

                if($deduct == 1){
                    if($request->cartDiscount==1){
                        return response()->json([
                            'status' => false,
                            'msg' => __('El cupón solo aplica para compras sin descuentos'),
                        ]);
                    }
                    if($request->cartDelivery==0){
                        return response()->json([
                            'status' => false,
                            'msg' => __('El cupón solo aplica para costos de envio, debe seleccionar una dirección de entrega'),
                        ]);
                    }
                    $deduct = $request->cartDelivery;
                }
                //$coupon->decrement('limit_to_num_uses');
                //$coupon->increment('used_count');
                return response()->json([
                    'deduct' => $deduct,
                    'status' => true,
                    'msg' => __('Coupon code applied successfully.'),
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'msg' => __('The coupon promotion code has been expired or the limit is exceeded.'),
        ]);
    }

  
}
