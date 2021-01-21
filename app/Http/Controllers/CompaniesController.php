<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use DataTables;

// Models
use App\Models\User;
use App\Models\Company;
use App\Models\Branch;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('companies.index');
    }

    public function list()
    {
        $data = Company::with(['city'])->where('deleted_at', NULL)->select('*')->get();

        // If you do not have permission for view list return null
        if(!User::permission('browse companies')->where('id', auth()->user()->id)->first()){
            return null;
        }

        return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('city', function($row){
                    return $row->city ? $row->city->name.' - '.$row->city->state : 'Undefined';
                })
                ->addColumn('logo', function($row){
                    return '<img src="'.url('storage/'.($row->logos == '../images/user.svg' ? $row->logos : str_replace('.', '-cropped.', $row->logos))).'" width="50px" />';
                })
                ->addColumn('action', function($row){
                        $actions = '
                                    <a href="'.route("companies.braches", ["company" => $row->id]).'" class="edit btn btn-dark btn-sm">
                                        <i class="fas fa-store"></i> <span class="hidden-sm">Sucursal</span>
                                    </a>
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
                ->rawColumns(['city', 'logo', 'action'])
                ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $route = $request->back_route ? 'companies.create' : 'companies.index';
        DB::beginTransaction();
        
        try {
            $validatedData = $request->validate([
                'owner_id' => 'required',
                'name' => 'required|max:191',
                'short_description' => 'max:191',
                'city_id' => 'required',
                'phones' => 'required|max:191',
                'address' => 'required|max:191',
                'logo' => 'required',
            ]);

            $logo = $this->save_image($request->file('logo'), 'companies');
            $banner = $this->save_image($request->file('banner'), 'companies');

            $company = Company::create([
                'owner_id' => $request->owner_id,
                'name' => $request->name,
                'slogan' => $request->slogan,
                'short_description' => $request->short_description,
                'long_description' => $request->long_description,
                'city_id' => $request->city_id,
                'address' => $request->address,
                'phones' => $request->phones,
                'logos' => $logo,
                'banners' => $banner
            ]);

            DB::commit();
            return redirect()->route($route)->with(['message' => 'Nuevo restaurante agregado', 'alert-type' => 'success']);
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

    // *************** Branches ***************

    public function braches($company_id){
        $company = Company::findOrFail($company_id);
        return view('companies.branches.index', compact('company'));
    }

    public function braches_list($company_id)
    {
        $data = Branch::where('deleted_at', NULL)->select('*')->get();
        if(!User::permission('browse companies')->where('id', auth()->user()->id)->first()){
            return null;
        }

        return Datatables::of($data)
                ->addIndexColumn()
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
                ->rawColumns(['action'])
                ->make(true);
    }

    public function braches_create($company_id){
        $company = Company::findOrFail($company_id);
        return view('companies.branches.create', compact('company'));
    }
}
