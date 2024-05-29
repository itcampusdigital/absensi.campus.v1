<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Group;
use App\Models\ReportDaily;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportDailyController extends Controller
{
    public function index(Request $request)
    {
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get the month and year
        $month = $request->query('month') ?: date('m');
        $year = $request->query('year') ?: date('Y');
        
        $group_id = Auth::user()->role_id == 2 ? Auth::user()->group_id : $request->group;


        $t1 = $request->t1 == null ? date('Y-m-d') : DateTime::createFromFormat('d/m/Y', $request->t1)->format('Y-m-d');
        $t2 = $request->t2 == null ? date('Y-m-d') : DateTime::createFromFormat('d/m/Y', $request->t2)->format('Y-m-d');
        $date_filter = $t1 >= $t2 ? [$t2,$t1] : [$t1,$t2];

        $office_id = $request->office == null ? null : $request->office;
        $position_id = $request->position == null ? null : $request->position;

            if($position_id == 0 && $office_id ){
                $dailies = ReportDaily::whereHas('user', function($query) use ($group_id, $office_id){
                    return $query->where('group_id', $group_id)->where('office_id', $office_id);
                })
                ->whereBetween('date',$date_filter)->get();
            }
            else if($position_id != 0 && $office_id){
                $dailies = ReportDaily::whereHas('user', function($query) use ($group_id, $office_id,$position_id){
                    return $query->where('group_id', $group_id)->where('office_id', $office_id)->where('position_id', $position_id);
                })
                ->whereBetween('date',$date_filter)->get();
            }
            else if($position_id != 0 && $office_id == 0){
                $dailies = ReportDaily::whereHas('user', function($query) use ($group_id,$position_id){
                    return $query->where('group_id', $group_id)->where('position_id', $position_id);
                })
                ->whereBetween('date',$date_filter)->get();  
            }
            else if($group_id == 0){
                $dailies = ReportDaily::whereBetween('date',$date_filter)->get();
            }
            else{
                // dd('cek');
                $dailies = ReportDaily::whereHas('user', function($query) use ($group_id){
                    return $query->where('group_id', $group_id);
                })
                ->whereBetween('date',$date_filter)->get();  
            }

        $groups = Group::orderBy('name','asc')->get();

        foreach($dailies as $daily){
            $daily->report = json_decode($daily->report);
        }

        // dd($dailies[0]->report[0]);

        return view('admin.report.index',[
            'groups' => $groups,
            'dailies' => $dailies
        ]);
    }
}
