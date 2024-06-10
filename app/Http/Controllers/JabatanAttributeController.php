<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Divisi;
use Illuminate\Http\Request;
use App\Models\JabatanAttribute;

class JabatanAttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $groups = Group::orderBy('name','asc')->get();
        $user_divisi = JabatanAttribute::where('division_id',$request->id_divisi)->get();
        $divisi = Divisi::where('id',$request->id_divisi)->first();
        return view('admin/divisi/jabatan_attr/index',[
            'groups' => $groups,
            'divisi'=>$divisi,
            'divisi_id' => $request->id_divisi,
            'user_divisi' => $user_divisi
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $groups = Group::orderBy('name','asc')->get();

        return view('admin.divisi.jabatan_attr.create',[
            'groups' => $groups,
            'id_divisi' => $request->id_divisi
        ]);
    }


}
