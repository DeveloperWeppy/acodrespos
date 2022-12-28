<?php

namespace App\Http\Controllers\Items;

use App\Items;
use App\Extras;
use App\Models\Log;
use App\Models\Variants;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class VariantsController extends Controller
{
    private function getOptionsForItem(Items $item)
    {
        $options = [];
        foreach ($item->options->toArray() as $option) {
            $data = [];
            foreach (explode(',', $option['options']) as $key => $value) {
                $data[str_replace(' ', '-', mb_strtolower(trim($value)))] = $value;
            }
            array_push($options, ['id'=>$option['id'], 'name'=>$option['name'], 'data'=>$data]);
        }

        return [
            ['ftype'=>'multiselect', 'name'=>'Options', 'id'=>'option', 'placeholder'=>'Enter option','data'=>$options, 'required'=>true],
        ];
    }

    private function getFields(Items $item)
    {
        return array_merge([
            ['ftype'=>'input', 'type'=>'number', 'name'=>'Price', 'id'=>'price', 'placeholder'=>'Ingrese el precio de la variante', 'required'=>true],
        ], $this->getOptionsForItem($item));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Items $item)
    {
        return view('items.variants.index', ['setup' => [
            'title'=>__('Variants for')." ".$item->name,
            'action_link'=>route('items.variants.create', ['item'=>$item->id]),
            'action_name'=>'Add new variant',
            'items'=>$item->uservariants()->paginate(10),
            'item_names'=>'variants',
            'breadcrumbs'=>[
                [__('Menu'), '/items'],
                [$item->name, '/items/'.$item->id.'/edit'],
                [__('Variants'), null],
            ],
        ]]);
    }

    public function extras(Variants $variant)
    {
        $theExtras = $variant->extras->toArray();
        $theExtrasGlobal = Extras::where('extra_for_all_variants', 1)->where('item_id', $variant->item_id)->get()->toArray();

        return response()->json([
            'data' => array_merge($theExtras, $theExtrasGlobal),
            'status' => true,
            'errMsg' => '',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Items $item)
    {
        if ($item->options->count() == 0) {
            return redirect()->route('items.options.create', ['item'=>$item->id])->withError(__('First, you will need to add some options. Add the item first option now'));
        }

        return view('general.form', ['setup' => [
            'title'=>__('New variant for')." ".$item->name,
            'action_link'=>route('items.variants.index', ['item'=>$item->id]),
            'action_name'=>__('Back'),
            'iscontent'=>true,
            'action'=>route('items.variants.store', ['item'=>$item->id]),
            'breadcrumbs'=>[
                [__('Menu'), '/items'],
                [$item->name, '/items/'.$item->id.'/edit'],
                [__('Variants'), route('items.variants.index', ['item'=>$item->id])],
                [__('New'), null],
            ],
        ],
        'fields'=>$this->getFields($item), ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Items $item, Request $request)
    {
        $function = $this->getIpLocation();
        $variant = Variants::create([
            'price'=>$request->price,
            'item_id'=>$item->id,
            'options'=>json_encode($request->option),
        ]);
        $variant->save();
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $request->ip(),
            'module' => 'MENÚ',
            'submodule' => 'VARIANTE DE PRODUCTO',
            'action' => 'Registro',
            'detail' => 'Registro de la nueva variante "' .$request->name .'", al producto '.$item->name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        $this->doUpdateOfSystemVariants($variant->item);

        return redirect()->route('items.edit', $item->id)->withStatus(__('Item successfully updated.'));

        //return redirect()->route('items.variants.index', ['item'=>$item->id])->withStatus(__('Variant has been added'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Variants $variant)
    {
        $fields = $this->getFields($variant->item);
        $fields[0]['value'] = $variant->price;

        //Now fill the options
        if (is_object(json_decode($variant->options))) {
            foreach (json_decode($variant->options, true) as $key => $value) {
                foreach ($fields[1]['data'] as &$option) {
                    if ($option['id'].'' == $key.'') {
                        $option['value'] = $value;
                    }
                }
            }
        }
        return view('general.form', ['setup' => [
            'title'=>__('Edit variant').' #'.$variant->id,
            'action_link'=>route('items.variants.index', ['item'=>$variant->item]),
            'action_name'=>__('Back'),
            'iscontent'=>true,
            'isupdate'=>true,
            'action'=>route('items.variants.update', ['variant'=>$variant->id]),
            'breadcrumbs'=>[
                [__('Menu'), '/items'],
                [$variant->item->name, '/items/'.$variant->item->id.'/edit'],
                [__('Variants'), route('items.variants.index', ['item'=>$variant->item->id])],
                ['#'.$variant->id, null],
            ],
        ],
        'fields'=>$fields, ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Variants $variant)
    {
        $function = $this->getIpLocation();
        $variant->price = $request->price;
        $variant->options = json_encode($request->option);
        $variant->update();
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $request->ip(),
            'module' => 'MENÚ',
            'submodule' => 'VARIANTE DE PRODUCTO',
            'action' => 'Actualización',
            'detail' => 'Se actualizó la variante "' .$request->name .'", al producto '.$variant->item->name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        $this->doUpdateOfSystemVariants($variant->item);

        return redirect()->route('items.edit', $variant->item->id)->withStatus(__('Variant has been updated'));
        //return redirect()->route('items.variants.index', ['item'=>$variant->item->id])->withStatus(__('Variant has been updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Variants $variant)
    {
        $item=$variant->item;
        $iteId = $variant->item->id;
        $function = $this->getIpLocation();
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $function->ip,
            'module' => 'MENÚ',
            'submodule' => 'OPCIÓN DE PRODUCTO',
            'action' => 'Eliminación',
            'detail' => 'Se eliminó la opción, "' .$variant->name .'" del producto '.$variant->item->name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        $variant->delete();
        $this->doUpdateOfSystemVariants($item);

        return redirect()->route('items.edit', $iteId)->withStatus(__('Variant has been removed'));

        //return redirect()->route('items.variants.index', ['item'=>$variant->item->id])->withStatus(__('Variant has been removed'));
    }

    private function doUpdateOfSystemVariants(Items $item){
        if($item->enable_system_variants==1){
            //Delete all system 
            $item->systemvariants()->forceDelete();
            $item->makeAllMissingVariants($item->price);
        }
    }
}
