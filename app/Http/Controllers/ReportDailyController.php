<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\Group;
use App\Models\Divisi;
use App\Models\ReportDaily;
use Illuminate\Http\Request;
use App\Models\JabatanAttribute;
use Illuminate\Support\Facades\Auth;
use App\Models\JobDutyResponsibility;

class ReportDailyController extends Controller
{
    public function rekapApi(Request $request)
    {
        return $request->id;
    }

    public function getReport(Request $request)
    {
        $id = $request->id;
        $report = ReportDaily::where('id','=',$id)->first();
        $reports_id = json_decode($report->report);
        
        $dailyJob = Divisi::select('id','name','tugas')->where('id', $report->division_id)->first();
        $tugasJob = json_decode($dailyJob->tugas);

        $data_tugas = array();
        for($i=0;$i<count($reports_id);$i++){
            if($reports_id[$i]->id_tugas == $tugasJob[$i]->id_tugas){
                $data_tugas[$i]['id_tugas'] = $tugasJob[$i]->id_tugas;
                $data_tugas[$i]['tugas'] = $tugasJob[$i]->tugas;
                $data_tugas[$i]['target'] = $tugasJob[$i]->target;
                $data_tugas[$i]['score'] = $reports_id[$i]->score;
            }
        }

        $data = array();
        $data['nama'] = $report->user->name;
        $data['date'] = $report->date;
        $data['note'] = $report->note;
        $data['dailyReport'] = $data_tugas;

        return response()->json($data);
    }

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

        return view('admin.report.index',[
            'groups' => $groups,
            'dailies' => $dailies
        ]);
    }

    public function indexRekap(Request $request)
    {
        has_access(method(__METHOD__), Auth::user()->role_id);
        $groups = Group::orderBy('name','asc')->get();
        $divisi = Divisi::select('id','name')->get();

        if($request->bulan || $request->divisi){
            $get_user_divisi = JabatanAttribute::select('id','user_id','division_id')->where('division_id','=',$request->divisi)->get();
        }

        return view('admin.report.rekap',[
            'groups' => $groups,
            'divisis' => $divisi,
            // 'users_divisi' => $get_user_divisi

        ]);

    }
}
