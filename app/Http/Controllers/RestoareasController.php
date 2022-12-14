<?php

namespace App\Http\Controllers;

use App\RestoArea;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Akaunting\Module\Facade as Module;

class RestoareasController extends Controller
{
    /**
     * Provide class.
     */
    private $provider = RestoArea::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'admin.restaurant.restoareas.';

    /**
     * View path.
     */
    private $view_path = 'restoareas.';

    /**
     * Parameter name.
     */
    private $parameter_name = 'restoarea';

    /**
     * Title of this crud.
     */
    private $title = 'area';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'areas';

    /**
     * Auth checker functin for the crud.
     */
    private function authChecker()
    {
        $this->ownerOnly();
    }

    /**
     * List of fields for edit and create.
     */
    private function getFields()
    {
        return [
            ['ftype'=>'input', 'name'=>'Name', 'id'=>'name', 'placeholder'=>__('crud.enter_item_name', ['item'=>__($this->title)]), 'required'=>true],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authChecker();

        return view($this->view_path.'index', ['setup' => [
            'title'=>__('crud.item_managment', ['item'=>__($this->titlePlural)]),
            'action_link'=>route($this->webroute_path.'create'),
            'action_name'=>__('crud.add_new_item', ['item'=>__($this->title)]),
            'action_link2'=>route('admin.restaurant.tables.index')."?do_not_redirect=true",
            'action_name2'=>__('Tables'),
            'items'=>$this->getRestaurant()->areas()->paginate(config('settings.paginate')),
            'item_names'=>$this->titlePlural,
            'webroute_path'=>$this->webroute_path,
            'fields'=>$this->getFields(),
            'parameter_name'=>$this->parameter_name,
            'hasFloorPlan'=>Module::has('floorplan')
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

        return view('general.form', ['setup' => [
            'title'=>__('crud.new_item', ['item'=>__($this->title)]),
            'action_link'=>route($this->webroute_path.'index'),
            'action_name'=>__('crud.back'),
            'iscontent'=>true,
            'action'=>route($this->webroute_path.'store'),
        ],
        'fields'=>$this->getFields(), ]);
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
        $this->authChecker();
        $item = $this->provider::create([
            'name'=>$request->name,
            'restaurant_id'=>$this->getRestaurant()->id,
        ]);
        $item->save();

        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $request->ip(),
            'module' => 'ÁREAS DE RESTAURANTE',
            'submodule' => '',
            'action' => 'Registro',
            'detail' => 'Registro de Nueva área, ' .$request->name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_added', ['item'=>__($this->title)]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RestoArea  $restoArea
     * @return \Illuminate\Http\Response
     */
    public function show(RestoArea $restoArea)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $fields = $this->getFields();
        $fields[0]['value'] = $item->name;

        $parameter = [];
        $parameter[$this->parameter_name] = $id;

        return view('general.form', ['setup' => [
            'title'=>__('crud.edit_item_name', ['item'=>__($this->title), 'name'=>$item->name]),
            'action_link'=>route($this->webroute_path.'index'),
            'action_name'=>__('crud.back'),
            'iscontent'=>true,
            'isupdate'=>true,
            'action'=>route($this->webroute_path.'update', $parameter),
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
    public function update(Request $request, $id)
    {
        $function = $this->getIpLocation();
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->name = $request->name;
        $item->update();

        Log::create([
            'user_id' => Auth::user()->id,
            'ip' => $request->ip(),
            'module' => 'ÁREAS DE RESTAURANTE',
            'submodule' => '',
            'action' => 'Actualización',
            'detail' => 'Se actualizó el área, ' .$request->name,
            'country' => $function->country,
            'city' =>$function->city,
            'lat' =>$function->lat,
            'lon' =>$function->lon,
        ]);
        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_updated', ['item'=>__($this->title)]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $function = $this->getIpLocation();
        $this->authChecker();
        $item = $this->provider::find($id);
        $name_area= $item->name;
       
        if(isset($item->tables)){
            if ($item->tables->count() > 0) {
                return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_items_associated', ['item'=>__($this->title)]));
            } else {
                Log::create([
                    'user_id' => Auth::user()->id,
                    'ip' => $function->ip,
                    'module' => 'ÁREAS DE RESTAURANTE',
                    'submodule' => '',
                    'action' => 'Eliminación',
                    'detail' => 'Se eliminó el área, ' .$name_area,
                    'country' => $function->country,
                    'city' =>$function->city,
                    'lat' =>$function->lat,
                    'lon' =>$function->lon,
                ]);
                $item->delete();

                return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_removed', ['item'=>__($this->title)]));
            }
        }else{
            return redirect()->route($this->webroute_path.'index')->with("error","El area ya no existe");
        }

    
       
    }
}
