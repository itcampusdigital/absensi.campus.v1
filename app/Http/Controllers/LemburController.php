<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Leave;
use App\Models\Lembur;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Ajifatur\Helpers\DateTimeExt;


class LemburController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get the year
        $year = $request->query('year') ?: date('Y');
        $status = $request->query('status');
        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        if(Auth::user()->role_id == role('super-admin')) {
            // Get group
            $group = Group::find($request->query('group'));
            // Get leaves
            if($group && $status != 0) {
                $office = $request->query('office');
                $lembur = Lembur::has('user')->whereHas('user', function ($query) use ($group, $office) {
                    return $query->where('group_id','=',$group->id)->where('office_id','=',$office);
                })->where('status','=', $status)->whereYear('date',$year)->orderBy('date','desc')->get();
            }
            else{
                $lembur = Lembur::whereYear('date',$year)->orderBy('date','desc')->get();
            }
        }
        elseif(Auth::user()->role_id == role('admin')) {
            // Get leaves
            $group = Auth::user()->group_id;
            $office = $request->query('office');

            if($status != 0 && $office) {
                $lembur = Lembur::has('user')->whereHas('user', function ($query) use ($group, $office) {
                    return $query->where('group_id','=',$group)->where('office_id','=',$office);
                })->where('status','=', $status)->whereYear('date',$year)->orderBy('date','desc')->get();
            }else if($office){
                $lembur = Lembur::has('user')->whereHas('user', function ($query) use ($group, $office) {
                    return $query->where('group_id','=',$group)->where('office_id','=',$office);
                })->whereYear('date',$year)->orderBy('date','desc')->get();
            }
            else{
                $lembur = Lembur::has('user')->whereHas('user', function ($query) use ($group, $office) {
                    return $query->where('group_id','=',$group);
                })->whereYear('date',$year)->orderBy('date','desc')->get();
            }
        }
        elseif(Auth::user()->role_id == role('manager')) {
            // Get the user
            $user = User::findOrFail(Auth::user()->id);
         
            // Get leaves
            $group = Auth::user()->group_id;
            $office = $request->query('office');
            $lembur = Lembur::has('user')
                    ->whereHas('user', function ($query) use ($user, $group, $office) {
                        return $query->where('group_id','=',$group)
                        ->where('office_id','=',$office)
                        ->whereIn('office_id',$user->managed_offices()->pluck('office_id')->toArray());
            })->whereYear('date',$year)->orderBy('date','desc')->get();
        }

        // View
        return view('admin/lembur/index', [
            'year' => $year,
            'lemburs' => $lembur,
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check the access
        // has_access(method(__METHOD__), Auth::user()->role_id);

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/lembur/create', [
            'groups' => $groups
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

        $validator = Validator::make($request->all(), [
            'group_id' => Auth::user()->role_id == role('super-admin') ? 'required' : '',
            'office_id' => 'required',
            'user_id' => 'required',
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the leave
            $lembur = new Lembur;
            $lembur->user_id = $request->user_id;
            $lembur->start_time = $request->start_time;
            $lembur->end_time = $request->end_time;
            $lembur->keterangan = $request->keterangan;
            $lembur->status = 1;
            $lembur->date = DateTimeExt::change($request->date);
            $lembur->save();

            // Redirect
            return redirect()->route('admin.lembur.index')->with(['message' => 'Berhasil menambah data.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Lembur  $lembur
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->id;
        if(Auth::user()->role_id == role('super-admin')) {
            // Get the leave
            $lembur = Lembur::findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('admin')) {
            // Get the group
            $group = Auth::user()->group_id;

            // Get offices
            $offices = Auth::user()->group->offices()->pluck('id')->toArray();

            // Get the leave
            $lembur = Lembur::whereHas('user', function ($query) use ($group, $offices) {
                return $query->where('group_id','=',$group)->whereIn('office_id',$offices);
            })->findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            // Get the group
            $group = Auth::user()->group_id;

            // Get offices
            $offices = Auth::user()->managed_offices()->pluck('office_id')->toArray();

            // Get the leave
            $lembur = Lembur::whereHas('user', function ($query) use ($group, $offices) {
                return $query->where('group_id','=',$group)->whereIn('office_id',$offices);
            })->findOrFail($id);
        }

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/lembur/edit', [
            'lembur' => $lembur,
            'groups' => $groups,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Lembur  $lembur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the leave
            $lembur = Lembur::find($request->id);
            $lembur->start_time = $request->start_time;
            $lembur->end_time = $request->end_time;
            $lembur->keterangan = $request->keterangan;
            $lembur->date = DateTimeExt::change($request->date);
            $lembur->save();

            // Redirect
            return redirect()->route('admin.lembur.index')->with(['message' => 'Berhasil Merubah data.']);
        }
    }

    public function approval(Request $request)
    {
        $lembur = Lembur::find($request->id);
        $lembur->status = $request->status;
        $lembur->save();

        return redirect()->route('admin.lembur.index')->with(['message' => 'Berhasil Approval data.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Lembur  $lembur
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {

        // Get the leave
        $lembur = Lembur::find($request->id);

        // Delete the leave
        $lembur->delete();

        // Redirect
        return redirect()->route('admin.lembur.index')->with(['message' => 'Berhasil menghapus data.']);
    }
}
