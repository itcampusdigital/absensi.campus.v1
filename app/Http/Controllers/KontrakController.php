<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Group;
use App\Models\Kontrak;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Ajifatur\Helpers\DateTimeExt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class KontrakController extends Controller
{
    public function getKontrak(Request $request)
    {
        
        $office_select = $request->office_select;
        if($office_select){
            $user = Kontrak::with('user')
                    ->whereHas('user', function($query) use ($office_select){
                        return $query->where('office_id',$office_select)->whereNull('end_date');
                        
                    });

        }     
        else{
            $user = Kontrak::with('user')
                    ->whereHas('user', function($query){
                        return $query->whereNull('end_date');
                    });
                    
        }

        return DataTables::of($user)
                // ->addColumn('checkbox', '<input type="checkbox" class="form-check-input checkbox-one">')
                ->editColumn('user.office_id', function($query){
                    return $query->user->office != null ? $query->user->office->name : '';
                })
                ->editColumn('user.name', function($query){
                    $id = $query->user != null ? $query->user->id : '';
                    $name = $query->user != null ? $query->user->name : '';
                    return '<a href="'.route('admin.user.detail', ['id' => $query->user->id]).'">'.$query->user->name.'</a>';
                })
                ->editColumn('user.start_date', function($query){
                    $start_date = $query->user != null ? $query->user->start_date : '';

                    $conv_format = date('Y/m/d',strtotime($start_date));
                    $tb = '<span style=display:none>'.$conv_format.'</span>'.date('d/m/Y', strtotime($start_date));
                    return $tb;
                })
                ->editColumn('start_date_kontrak', function($query){
                    $conv_format = date('Y/m/d',strtotime($query->start_date_kontrak));
                    $tk = '<span style=display:none>'.$conv_format.'</span>'.date('d/m/Y', strtotime($query->start_date_kontrak)) ;
                    return $tk;
                })
                ->editColumn('masa', function($query){
                    $masa = '<div style="text-align:center">'.$query->masa.'</div>';
                    return $masa;
                })
                ->editColumn('end_date_kontrak', function($query){
                    $conv_format = date('Y/m/d',strtotime($query->end_date_kontrak));

                    $selisih = Carbon::parse(date('Y/m/d', time()))->diffInDays(Carbon::parse($conv_format),false);
                    $te = '<span style=display:none>'.$conv_format.'</span>'.date('d/m/Y', strtotime($query->end_date_kontrak));
                    $small_days = '<span class="bg-warning badge">'.$selisih.' Hari</span>';
                    if($selisih > 0){
                        $div = '<div class="mt-2">'.$te.'<br>'.$small_days.'</div>';
                        return $div;
                    }else{
                        $badge = '<span class="bg-danger badge">Tidak Aktif</span>';
                        $div = '<div class="mt-2">'.$te.'<br>'.$badge.'</div>';
                        return $div;
                    }
                    
                    
                })
                ->addColumn('action',function($query){
                    $delete = '<a href="'. route('admin.kontrak.destroy').'" data-id="'.$query->id.'" type="button" class="btn-delete btn btn-sm btn-danger"><i
                    class="bi-trash"></i></a>';
                    $link = '<a href="'. route('admin.kontrak.edit', $query->user_id).'" type="button" class="btn btn-sm btn-warning"><i
                    class="bi-pencil"></i></a>';

                    $div = '<div>'.$link.' '.$delete.'</div>';

                    return $div;
                })
                ->rawColumns(['user.office_id','user.name','start_date_kontrak','end_date_kontrak','user.start_date','action','masa'])
                // ->rawColumns(['user.name','user.office_id'])
                ->make(true);
    }

    public function index(Request $request)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);
        // $user = Kontrak::with('user')->get();

        $groups = Group::orderBy('name','asc')->get();

        
        return view('admin.kontrak.index', [
            // 'users' => $user,
            'groups' => $groups
        ]);
    }

    public function updateCuti(Request $request){
       
        $cuti = User::with(['kontrak'])->find($request->user_id);
        $cuti->kontrak->cuti = $request->cuti_tahunan;
        $cuti->kontrak->update();

        return Redirect::back()->with(['message' => 'Berhasil mengupdate data.']);
    }

    public function edit(Request $request)
    {
        has_access(method(__METHOD__), Auth::user()->role_id);
        $user_selected = User::with('kontrak')->find($request->id);
       

        return view('admin.kontrak.edit', [
            'user_select' => $user_selected,
  
        ]);
    }
    public function update(Request $request)
    {
        
        $validator = Validator::make($request->all(),[
            'masa' => 'required|numeric',
            'start_date_kontrak' => 'required',
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else{
            $user = User::find($request->id);
            $d = strval($request->masa);
            $request->start_date_kontrak = DateTimeExt::change($request->start_date_kontrak);
    
            $end_date = date('Y-m-d', strtotime($request->start_date_kontrak . '+' . $d . ' month'));
            
            DB::table('kontrak')
            ->where('user_id', $user->kontrak->user_id)
            ->update(['masa' => $request->masa]);
            DB::table('kontrak')
            ->where('user_id', $user->kontrak->user_id)
            ->update(['start_date_kontrak' => $request->start_date_kontrak]);
            DB::table('kontrak')
            ->where('user_id', $user->kontrak->user_id)
            ->update(['end_date_kontrak' => $end_date]);
            
            return redirect()->route('admin.kontrak.index')->with(['message' => 'Berhasil mengupdate data.']);
        }
    }

    public function destroy(Request $request)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        //find data
        $find_kontrak = Kontrak::find($request->id);

        $find_kontrak->delete();

        return redirect()->route('admin.kontrak.index')->with(['message' => 'Berhasil menghapus data.']);

    }
}
