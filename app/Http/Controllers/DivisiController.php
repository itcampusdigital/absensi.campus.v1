<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Divisi;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Models\JabatanAttribute;
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

        has_access(method(__METHOD__), Auth::user()->role_id);

        if($request->ajax()) {
            // Get offices by the group
            $divisi = Divisi::all();

            // Return
            return response()->json($divisi);
        }
        // $id_work =$request->id_work;
        $groups = Group::orderBy('name','asc')->get();
        $divisi = Divisi::select('id','name')->get();
        // View
        return view('admin/divisi/index', [
            'groups' => $groups,
            'divisi' => $divisi
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get groups
        $groups = Group::orderBy('name','asc')->get();
        $position = Position::where('group_id',Auth::user()->group_id)->orderBy('name','asc')->get();
        // View
        return view('admin/divisi/create', [
            'groups' => $groups,
            'positions' => $position
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
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'group_id' => Auth::user()->role_id == role('super-admin') ? 'required' : '',
            'name' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            $array_tugas = array();
            $array_tugas['tugas'] = $request->dr_names;
            $array_tugas['target'] = $request->target;

            $divisi = new Divisi;
            $divisi->name = $request->name;
            $divisi->group_id = Auth::user()->role_id == role('super-admin') ? $request->group_id : Auth::user()->group_id;
            $divisi->tugas = json_encode($array_tugas);
            $divisi->wewenang = json_encode($request->a_names);
            $divisi->save();

            return redirect()->route('admin.divisi.index')->with(['message' => 'Berhasil menambah data.']);

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
    public function edit(Request $request)
    {
        $groups = Group::orderBy('name','asc')->get();
        $wh_tugas = Divisi::find($request->id); 
        
        $tugas = $wh_tugas == null ? '' : json_decode($wh_tugas->tugas);
        $wewenang = $wh_tugas == null ? '' : json_decode($wh_tugas->wewenang);

        $count_tugas = $tugas == null ? 1 : count($tugas->tugas);
        $count_wewenang = $wewenang == null ? 1 : count($wewenang);

        // View
        return view('admin/divisi/edit', [
            'groups' => $groups,
            'wh_tugas' => $wh_tugas,
            'tugas' => $tugas,
            'wewenang' => $wewenang,
            'count_tugas' => $count_tugas,
            'count_wewenang' => $count_wewenang
        ]);
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
        $validator = Validator::make($request->all(), [
            'group_id' => Auth::user()->role_id == role('super-admin') ? 'required' : '',
            'name' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            $divisi = Divisi::find($request->id);
            $array_tugas = array();
            $array_tugas['tugas'] = $request->dr_names;
            $array_tugas['target'] = $request->target;

            $divisi->tugas = json_encode($array_tugas);
            $divisi->wewenang = json_encode($request->a_names);
            $divisi->save();

            return redirect()->route('admin.divisi.index')->with(['message' => 'Berhasil menambah data.']);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Divisi  $divisi
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $divisi = Divisi::find($request->id);
        $jabatanAttribute = JabatanAttribute::where('division_id','=',$divisi->id);
        $jabatanAttribute->delete();
        $divisi->delete();
        return redirect()->route('admin.divisi.index')->with(['message' => 'Berhasil menghapus data.']);
    }
}
