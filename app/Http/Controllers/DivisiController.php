<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Divisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DivisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id_work =$request->id_work;
        $groups = Group::orderBy('name','asc')->get();
        $jabatan_all = Divisi::where('group_id',Auth::user()->group_id)->where('workhour_id',$id_work)->orderBy('name','asc')->get();
        // View
        return view('admin/work-hour/divisi/index', [
            'groups' => $groups,
            'jabatans' => $jabatan_all
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id_tugas)
    {
        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/work-hour/divisi/create', [
            'groups' => $groups,
            'id_tugas' => $id_tugas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request->all());
        $validator = Validator::make($request->all(), [
            'group_id' => Auth::user()->role_id == role('super-admin') ? 'required' : '',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            $jabatan = new Divisi;
            $jabatan->group_id = Auth::user()->group_id;
            $jabatan->workhour_id = $request->workhour_id;
            $jabatan->save();

            return redirect()->route('admin.jabatan.index')->with(['message' => 'Berhasil menambah data.']);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Divisi  $divisi
     * @return \Illuminate\Http\Response
     */
    public function show(Divisi $divisi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Divisi  $divisi
     * @return \Illuminate\Http\Response
     */
    public function edit(Divisi $divisi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Divisi  $divisi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Divisi $divisi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Divisi  $divisi
     * @return \Illuminate\Http\Response
     */
    public function destroy(Divisi $divisi)
    {
        //
    }
}
