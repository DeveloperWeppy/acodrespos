<?php

namespace App\Http\Controllers;

use App\Categories;
use App\Models\AreaKitchen;
use Illuminate\Http\Request;

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
        $category = new Categories;
        $category->name = strip_tags($request->category_name);
        $category->restorant_id = $request->restaurant_id;
        $category->areakitchen_id = $request->areakitchen_id;
        $category->save();

        if (auth()->user()->hasRole('admin')) {
            //Direct to that page directly
            return redirect()->route('items.admin', ['restorant'=>$request->restaurant_id])->withStatus(__('Category successfully created.'));
        }

        return redirect()->route('items.index')->withStatus(__('Category successfully created.'));
    }

    public function storeareakitchen(Request $request)
    {
        $area = new AreaKitchen();
        $area->name = $request->name;
        $area->colorarea = $request->colorarea;
        $area->restorant_id = $request->restaurant_id;
        $area->save();

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
        $category->name = $request->category_name;
        $category->areakitchen_id = $request->areakitchen_idd;
        $category->update();

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
        $category->delete();
        return redirect()->route('items.index')->withStatus(__('Category successfully deleted.'));
    }
}
