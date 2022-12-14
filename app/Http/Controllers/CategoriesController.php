<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Models\Log;
use App\Models\AreaKitchen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $function = $this->getIpLocation();

        $name_count = Categories::where('name', $request->category_name)->where('restorant_id', $request->restaurant_id)->count();

        if ($name_count > 0) {
            
            if (auth()->user()->hasRole('admin')) {
                //Direct to that page directly
                return redirect()->route('items.admin', ['restorant'=>$request->restaurant_id])->with('error', 'La categoría que intenta registrar ya existe.');
            }

            return redirect()->route('items.index')->with('error', 'La categoría que intenta registrar ya existe.');

        } else {
            $category = new Categories;
            $category->name = strip_tags($request->category_name);
            $category->restorant_id = $request->restaurant_id;
            $category->areakitchen_id = $request->areakitchen_id;
            $category->save();

            Log::create([
                'user_id' => Auth::user()->id,
                'ip' => $request->ip(),
                'module' => 'MENU',
                'submodule' => 'CATEGORIA',
                'action' => 'Registro',
                'detail' => 'Registro de Categoría Nueva, ' .$request->category_name,
                'country' => $function->country,
                'city' =>$function->city,
                'lat' =>$function->lat,
                'lon' =>$function->lon,
            ]);

            if (auth()->user()->hasRole('admin')) {
                //Direct to that page directly
                return redirect()->route('items.admin', ['restorant'=>$request->restaurant_id])->withStatus(__('Category successfully created.'));
            }

            return redirect()->route('items.index')->withStatus(__('Category successfully created.'));
        }
    }

    public function storeareakitchen(Request $request)
    {
        $function = $this->getIpLocation();

        $area = new AreaKitchen();
        $area->name = $request->name;
        $area->colorarea = $request->colorarea;
        $area->restorant_id = $request->restaurant_id;
        $area->save();

        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $request->ip(),
            'module' => 'MENÚ',
            'submodule' => 'AREA DE COCINA',
            'action' => 'Registro',
            'detail' => 'Registro de Nueva área de cocina, ' .$request->category_name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);

        if (auth()->user()->hasRole('admin')) {
            //Direct to that page directly
            return redirect()->route('items.admin', ['restorant'=>$request->restaurant_id])->withStatus(__('Área de cocina creada correctamente.'));
        }

        return redirect()->route('items.index')->withStatus(__('Área de cocina creada correctamente.'));
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Categories $category)
    {
        $function = $this->getIpLocation();
        $category->name = $request->category_name;
        $category->areakitchen_id = $request->areakitchen_idd;
        $category->update();
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $request->ip(),
            'module' => 'MENÚ',
            'submodule' => 'CATEGORIA',
            'action' => 'Actualización',
            'detail' => 'Se actualizó la categoría, ' .$request->category_name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        return redirect()->back()->withStatus(__('Category name successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Categories $category)
    {
        $function = $this->getIpLocation();
        
        $category->delete();
        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $function->ip,
            'module' => 'MENÚ',
            'submodule' => 'CATEGORIA',
            'action' => 'Eliminación',
            'detail' => 'Se eliminó la categoría, ' .$category->name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        return redirect()->route('items.index')->withStatus(__('Category successfully deleted.'));
    }
}
