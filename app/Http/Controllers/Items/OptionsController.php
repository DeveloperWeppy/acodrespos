<?php

namespace App\Http\Controllers\Items;

use App\Items;
use App\Models\Log;
use App\Models\Options;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OptionsController extends Controller
{
    private function getFields()
    {
        return [
            ['ftype'=>'input', 'name'=>'Name', 'id'=>'name', 'placeholder'=>'Ingrese el nombre de la opción, Ejem: tamaño', 'required'=>true],
            ['ftype'=>'input', 'name'=>'Lista separada por comas del valor de la opción', 'id'=>'options', 'placeholder'=>'Ingrese una lista separada por comas de valores de opciones disponibles, ejem: Pequeño, mediano, grande', 'required'=>true],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Items $item)
    {
        return view('items.options.index', ['setup' => [
            'title'=>__('Options for').' '.$item->name,
            'action_link'=>route('items.options.create', ['item'=>$item->id]),
            'action_name'=>'Añadir Nueva Opción',
            'items'=>$item->options()->paginate(10),
            'item_names'=>'opciones',
            'breadcrumbs'=>[
                [__('Menu'), '/items'],
                [$item->name, '/items/'.$item->id.'/edit'],
                [__('Options'), null],
            ],
        ]]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Items $item)
    {
        return view('general.form', ['setup' => [
            'title'=>__('New option for').' '.$item->name,
            'action_link'=>route('items.options.index', ['item'=>$item->id]),
            'action_name'=>__('Back'),
            'iscontent'=>true,
            'action'=>route('items.options.store', ['item'=>$item->id]),
            'breadcrumbs'=>[
                [__('Menu'), '/items'],
                [$item->name, '/items/'.$item->id.'/edit'],
                [__('Options'), route('items.options.index', ['item'=>$item->id])],
                [__('New'), null],
            ],
        ],
        'fields'=>$this->getFields(), ]);
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
        $option = Options::create([
            'name'=>$request->name,
            'options'=> str_replace(', ', ',', $this->replace_spec_char($request->options)),
            'item_id'=>$item->id,
        ]);
        $option->save();
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $request->ip(),
            'module' => 'MENÚ',
            'submodule' => 'OPCIÓN DE PRODUCTO',
            'action' => 'Registro',
            'detail' => 'Registro de nueva opción "' .$request->name .'", al producto '.$item->name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        return redirect()->route('items.options.index', ['item'=>$item->id])->withStatus(__('Option has been added'));
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
    public function edit(Options $option)
    {
        $fields = $this->getFields();
        $fields[0]['value'] = $option->name;
        $fields[1]['value'] = $option->options;
        return view('general.form', ['setup' => [
            'title'=>__('Edit option').' '.$option->name,
            'action_link'=>route('items.options.index', ['item'=>$option->item]),
            'action_name'=>__('Back'),
            'iscontent'=>true,
            'isupdate'=>true,
            'action'=>route('items.options.update', ['option'=>$option->id]),
            'breadcrumbs'=>[
                [__('Menu'), '/items'],
                [$option->item->name, '/items/'.$option->item->id.'/edit'],
                [__('Options'), route('items.options.index', ['item'=>$option->item->id])],
                [$option->name, null],
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
    public function update(Request $request, Options $option)
    {
        $function = $this->getIpLocation();
        $option->name = $request->name;
        $option->options = str_replace(', ', ',', $this->replace_spec_char($request->options));
        $option->update();
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $request->ip(),
            'module' => 'MENÚ',
            'submodule' => 'OPCIÓN DE PRODUCTO',
            'action' => 'Actualización',
            'detail' => 'Se actualizó la opción "' .$request->name .'", del producto '.$option->item->name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        return redirect()->route('items.options.index', ['item'=>$option->item->id])->withStatus(__('Option has been updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Options $option)
    {
        $function = $this->getIpLocation();
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $function->ip,
            'module' => 'MENÚ',
            'submodule' => 'OPCIÓN DE PRODUCTO',
            'action' => 'Eliminación',
            'detail' => 'Se eliminó la opción, "' .$option->name .'" del producto '.$option->item->name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        $option->delete();
        
        return redirect()->route('items.options.index', ['item'=>$option->item->id])->withStatus(__('Option has been removed'));
    }
}
