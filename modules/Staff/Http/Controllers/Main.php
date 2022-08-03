<?php

namespace Modules\Staff\Http\Controllers;

use App\User;
use App\Tables;
use App\RestoArea;
use Illuminate\Support\Str;
use Illuminate\Http\Request;


use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Main extends Controller
{
    /**
     * Provide class.
     */
    private $provider = User::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'staff.';

    /**
     * View path.
     */
    private $view_path = 'staff::';

    /**
     * Parameter name.
     */
    private $parameter_name = 'table';

    /**
     * Title of this crud.
     */
    private $title = 'Personal';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'Personal';

    /**
     * Auth checker functin for the crud.
     */
    private function authChecker()
    {
        if (!auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function getFields()
    {
        return [
            ['class'=>'col-md-6', 'ftype'=>'input', 'name'=>'Name', 'id'=>'name', 'placeholder'=>'Nombre y Apellido', 'required'=>true],
            ['class'=>'col-md-6', 'ftype'=>'input', 'name'=>'Email', 'id'=>'email', 'placeholder'=>'Ingrese un Correo', 'required'=>true],
            ['class'=>'col-md-6', 'ftype'=>'input','type'=>"password", 'name'=>'Password', 'id'=>'password', 'placeholder'=>'Ingrese una contraseña', 'required'=>true],
        ];
    }

    private function getFieldsTable()
    {
        return [
            ['class'=>'col-md-6', 'ftype'=>'input', 'name'=>'Name', 'id'=>'name', 'placeholder'=>'Nombre y Apellido', 'required'=>true],
            ['class'=>'col-md-6', 'ftype'=>'input', 'name'=>'Email', 'id'=>'email', 'placeholder'=>'Ingrese un Correo', 'required'=>true],
            ['class'=>'col-md-6', 'ftype'=>'input', 'name'=>'Rol', 'id'=>'email', 'placeholder'=>'Ingrese un Correo', 'required'=>true],
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
        $fields=$this->getFieldsTable();
        unset($fields[3]);
        /* dd($this->getRestaurant()->staff()->with('roles')->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'owner');
        })->paginate(config('settings.paginate'))); */
        return view($this->view_path.'index', ['setup' => [
            'title'=>__('Administrar Personal', ['item'=>__($this->titlePlural)]),
            'action_link'=>route($this->webroute_path.'create'),
            'action_name'=>__('Añadir Nuevo', ['item'=>__($this->title)]),
            'items'=>$this->getRestaurant()->staff()->with('roles')->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'owner');
            })->paginate(config('settings.paginate')),
            'item_names'=>$this->titlePlural,
            'webroute_path'=>$this->webroute_path,
            'fields'=>$fields,
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
        $all_roles = Role::whereNotIn('name', ['admin', 'owner', 'driver', 'client'])->get();

        return view('general.form', ['setup' => [
            'inrow'=>true,
            'title'=>__('Nuevo Miembro', ['item'=>__($this->title)]),
            'action_link'=>route($this->webroute_path.'index'),
            'action_name'=>__('crud.back'),
            'iscontent'=>true,
            'action'=>route($this->webroute_path.'store'),
            'roles' => $all_roles,
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

        $this->authChecker();
        $item = $this->provider::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$request->password,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(80),
            'restaurant_id'=>$this->getRestaurant()->id,
        ]);
        $item->save();

        $item->assignRole($request->rol);

        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_added', ['item'=>__($this->title)]));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tables  $tables
     * @return \Illuminate\Http\Response
     */
    public function show(Tables $tables)
    {
        //
    }

    public function loginas($id){
        $this->authChecker();

        $staff=User::findOrFail($id);

        if ($staff->restaurant->user->id!=auth()->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        Auth::login($staff, true);
        return  redirect(route('home'));


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tables  $tables
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authChecker();
        
        $item = $this->provider::findOrFail($id);
        if (!$this->getRestaurant()->id==$item->restaurant_id) {
            abort(403, 'Unauthorized action.');
        }

        $fields = $this->getFields();
        $fields[0]['value'] = $item->name;
        $fields[1]['value'] = $item->email;

        $parameter = [];
        $parameter[$this->parameter_name] = $id;

        return view('general.form', ['setup' => [
            'inrow'=>true,
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
     * @param  \App\Tables  $tables
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->name = $request->name;
        $item->email = $request->email;
        if($request->password&&strlen( $request->password)>2){
            $item->password = Hash::make($request->password);
        }
        $item->update();

        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_updated', ['item'=>__($this->title)]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tables  $tables
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        if (!$this->getRestaurant()->id==$item->restaurant_id) {
            abort(403, 'Unauthorized action.');
        }
        $item->delete();
        return redirect()->route($this->webroute_path.'index')->withStatus(__('crud.item_has_been_removed', ['item'=>__($this->title)]));
    }
}


