<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use DataTables;

// Models
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('security.roles.index');
    }

    public function list()
    {
        $data = Role::all();
        
        if(!User::permission('browse roles')->where('id', auth()->user()->id)->first()){
            return null;
        }

        return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                        $url = "'".route('roles.destroy', ['role' => $row->id])."'";
                        $actions = '
                                    <a href="javascript:void(0)" class="edit btn btn-warning btn-sm">
                                        <i class="fas fa-eye"></i> <span class="hidden-sm">Ver</span>
                                    </a>
                                    <a href="'.route('roles.edit', ['role' => $row->id]).'" class="edit btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i> <span class="hidden-sm">Editar</span>
                                    </a>
                                    <a onclick="deleteRegister('.$url.')" data-toggle="modal" data-target="#deleteModal" class="edit btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> <span class="hidden-sm">Eliminar</span>
                                    </a>
                                ';
                        return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    // Lista de roles que puede asignar un propietario
    public function list_alt(){
        $roles = Role::where('id', '>', 2)->get();
        return response()->json(['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('security.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $route = $request->back_route ? 'roles.create' : 'roles.index';
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:191'],
                'guard_name' => ['required', 'string', 'max:191']
            ]);

            $user = Role::create([
                'name' => $request->name,
                'guard_name' => $request->guard_name
            ]);
            DB::commit();
            return redirect()->route($route)->with(['message' => 'Nuevo rol agregado', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route($route)->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
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
        $role = Role::find($id);
        $permissions = Permission::all();
        return view('security.roles.edit', compact('role', 'permissions'));
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
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:191'],
                'guard_name' => ['required', 'string', 'max:191']
            ]);

            $role = Role::find($id);
            $role->name = $request->name;
            $role->guard_name = $request->guard_name;
            $role->save();

            $role->syncPermissions($request->permission_id);

            DB::commit();
            return redirect()->route('roles.index')->with(['message' => 'Rol actualizado', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('roles.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $role = Role::destroy($id);

            DB::commit();
            return redirect()->route('roles.index')->with(['message' => 'Rol eliminado', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('roles.index')->with(['message' => 'Ocurrió un error', 'alert-type' => 'error']);
        }
    }
}
