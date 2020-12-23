<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use DataTables;

// Models
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Person;
use App\Models\Owner;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('security.users.index');
    }

    public function list()
    {
        $data = User::where('deleted_at', NULL)->select('*')->get();
        
        if(!User::permission('browse users')->where('id', auth()->user()->id)->first()){
            return null;
        }

        return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('img', function($row){
                    return '<img src="'.url('storage/'.($row->avatar == '../images/user.svg' ? $row->avatar : str_replace('.', '-cropped.', $row->avatar))).'" width="50px" />';
                })
                ->addColumn('action', function($row){
                        $actions = '
                                    <a href="javascript:void(0)" class="edit btn btn-warning btn-sm">
                                        <i class="fas fa-eye"></i> <span class="hidden-sm">Ver</span>
                                    </a>
                                    <a href="javascript:void(0)" class="edit btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i> <span class="hidden-sm">Editar</span>
                                    </a>
                                    <a href="javascript:void(0)" class="edit btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> <span class="hidden-sm">Eliminar</span>
                                    </a>
                                ';
                        return $actions;
                })
                ->rawColumns(['img', 'action'])
                ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('security.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $route = $request->back_route ? 'users.create' : 'users.index';
        DB::beginTransaction();
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:191'],
                'email' => ['required', 'string', 'email', 'max:191', 'unique:users'],
                'password' => ['required', 'string', 'min:8'],
                'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);

            $avatar = $this->save_image($request->file('avatar'), 'users');

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'avatar' => $avatar ?? '../images/user.svg',
            ]);

            // Asign role
            $user->assignRole(Role::find($request->role_id)->name);

            // create person
            $person = Person::create([
                'first_name' => $request->name,
            ]);

            switch (Role::find($request->role_id)->name) {
                case 'owner':
                    Owner::create([
                        'person_id' => $person->id,
                        'user_id' => $user->id
                    ]);
                    break;
            }

            DB::commit();
            
            // Returns json for the API requests
            if($request->api){
                return response()->json(['user' => $user]);
            }

            return redirect()->route($route)->with(['message' => 'Nuevo usuario agregado', 'alert-type' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            // Returns json for the API requests
            if($request->api){
                return response()->json(['error' => $e]);
            }
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
