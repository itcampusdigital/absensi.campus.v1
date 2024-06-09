<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use App\Models\WorkHourTugas;

class WorkHourTugasController extends Controller
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
    public function create($id_tugas)
    {
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

        $wh_tugas = new WorkHourTugas;
        $wh_tugas->workhour_id = $request->workhour_id;
        $wh_tugas->tugas = json_encode($request->dr_names);
        $wh_tugas->wewenang = json_encode($request->a_names);
        $wh_tugas->save();

        // Redirect
        return redirect()->route('admin.work-hour.index')->with(['message' => 'Berhasil menambah Tugas.']); 
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WorkHourTugas  $workHourTugas
     * @return \Illuminate\Http\Response
     */
    public function show(WorkHourTugas $workHourTugas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WorkHourTugas  $workHourTugas
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $groups = Group::orderBy('name','asc')->get();
        $wh_tugas = WorkHourTugas::where('workhour_id',$request->id_tugas)->first(); 
        $tugas = $wh_tugas == null ? '' : json_decode($wh_tugas->tugas);
        $wewenang = $wh_tugas == null ? '' : json_decode($wh_tugas->wewenang);

        $count_tugas = $tugas == null ? 1 : count($tugas->tugas);
        $count_wewenang = $wewenang == null ? 1 : count($wewenang);

        // View
        return view('admin/work-hour/divisi/edit', [
            'id_tugas' => $request->id_tugas,
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
     * @param  \App\Models\WorkHourTugas  $workHourTugas
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, WorkHourTugas $workHourTugas)
    {
        // dd($request->all());
        $wh_tugas = WorkHourTugas::where('workhour_id',$request->workhour_id)->first();
        // dd($wh_tugas);
        if($wh_tugas == null){
            $wh_tugas = new WorkHourTugas;
            $wh_tugas->workhour_id = $request->workhour_id;
            if($request->dr_names[0] != null){
                $arrayTugas = array();
                $arrayTugas['tugas'] = $request->dr_names;
                $arrayTugas['target'] = $request->target;

                $wh_tugas->tugas = json_encode($arrayTugas);
            }

            $cek = $request->a_names[0] != null ? json_encode($request->a_names) : null;

            $wh_tugas->wewenang = $cek;
            $wh_tugas->save();
        }else{
            if($request->dr_names[0] != null){
                $arrayTugas = array();
                $arrayTugas['tugas'] = $request->dr_names;
                $arrayTugas['target'] = $request->target;
                $wh_tugas->tugas = json_encode($arrayTugas);
            }

            $cek = $request->a_names[0] != null ? json_encode($request->a_names) : null;
            $wh_tugas->wewenang = $cek;
            $wh_tugas->save();
        }

        return redirect()->route('admin.work-hour.index')->with(['message' => 'Berhasil menambah Tugas.']); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WorkHourTugas  $workHourTugas
     * @return \Illuminate\Http\Response
     */
    public function destroy(WorkHourTugas $workHourTugas)
    {
        //
    }
}
