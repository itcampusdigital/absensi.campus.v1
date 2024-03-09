<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Group;
use App\Models\Leave;
use App\Models\Absent;
use App\Models\WorkHour;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Ajifatur\Helpers\DateTimeExt;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SummaryAttendanceExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class SummaryAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);
        // Set params
        $dt1 = date('m') > 1 ? date('Y-m-d', strtotime(date('Y').'-'.(date('m')-1).'-24')) : date('Y-m-d', strtotime((date('Y')-1).'-12-24'));
        $dt2 = date('Y-m-d', strtotime(date('Y').'-'.date('m').'-23'));
        $t1 = $request->query('t1') != null ? DateTimeExt::change($request->query('t1')) : $dt1;
        $t2 = $request->query('t2') != null ? DateTimeExt::change($request->query('t2')) : $dt2;
		
		// Set the status and status sign
        $status = $request->query('status') != null ? $request->query('status') : 1;
        $statusSign = $status == 1 ? '=' : '!=';

        if(Auth::user()->role_id == role('super-admin')) {
            
            // Set params
            $group = $request->query('group') != null ? $request->query('group') : 0;
            $office = $request->query('office') != null ? $request->query('office') : 0;
            $position = $request->query('position') != null ? $request->query('position') : 0;

            // Get users
            if($group != 0 && $office != 0 && $position != 0){

                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('office_id','=',$office)->where('position_id','=',$position)
                ->where('end_date',$statusSign,null)->get();
            }
            elseif($group != 0 && $office == 0 && $position != 0)
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)
                ->where('position_id','=',$position)->where('end_date',$statusSign,null)->get();

            elseif($group != 0 && $office != 0 && $position == 0)
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)
                ->where('office_id','=',$office)->where('end_date',$statusSign,null)->get();

            elseif($group != 0 && $office == 0 && $position == 0)
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)
                ->where('end_date',$statusSign,null)->get();
            else
                $users = User::where('role_id','=',role('member'))->where('end_date',$statusSign,null)->get();
        }
        elseif(Auth::user()->role_id == role('admin')) {
            // Set params
            $group = Auth::user()->group_id;
            $office = $request->query('office') != null ? $request->query('office') : 0;
            $position = $request->query('position') != null ? $request->query('position') : 0;

            // Get users
            if($office != 0 && $position != 0)
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('office_id','=',$office)->where('position_id','=',$position)
					->where('end_date',$statusSign,null)->get();

            else if($office != 0 && $position == 0)
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('office_id','=',$office)
                ->where('end_date',$statusSign,null)->get();

            else if($office == 0 && $position != 0)
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('position_id','=',$position)
                ->where('end_date',$statusSign,null)->get();

            else
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('end_date',$statusSign,null)->get();
        }
        elseif(Auth::user()->role_id == role('manager')) {
            // Set params
            $user = User::findOrFail(Auth::user()->id);
            $group = Auth::user()->group_id;
            $office = $request->query('office') != null ? $request->query('office') : 0;

            // Get users
            if($office == 0)
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('end_date',$statusSign,null)->whereIn('office_id',$user->managed_offices()->pluck('office_id')->toArray())->get();
            elseif(in_array($office, $user->managed_offices()->pluck('office_id')->toArray()))
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('end_date',$statusSign,null)->whereIn('office_id',$user->managed_offices()->pluck('office_id')->toArray())->where('office_id','=',$office)->get();
            else
                abort(403);
        }

        // Set users attendances and absents
        if(count($users) > 0) {
            foreach($users as $key=>$user) {
                // Set absents
                $users[$key]->absent1 = Absent::where('user_id','=',$user->id)->where('category_id','=',1)->where('date','>=',$t1)->where('date','<=',$t2)->count();
                $users[$key]->absent2 = Absent::where('user_id','=',$user->id)->where('category_id','=',2)->where('date','>=',$t1)->where('date','<=',$t2)->count();

                // Set leaves
                $users[$key]->leave = Leave::where('user_id','=',$user->id)->where('date','>=',$t1)->where('date','<=',$t2)->count();

                // Get the work hours
                $users[$key]->workhours = WorkHour::where('group_id','=',$user->group_id)->where('office_id','=',$user->office_id)->where('position_id','=',$user->position_id)->orderBy('name','asc')->get();

                if(count($users[$key]->workhours) > 0) {
                    foreach($users[$key]->workhours as $key2=>$workhour) {
                        // Get attendances
                        $attendances = Attendance::where('user_id','=',$user->id)->where('workhour_id','=',$workhour->id)->where('date','>=',$t1)->where('date','<=',$t2)->get();

                        // Count late
                        $late = 0;
                        foreach($attendances as $attendance) {
                            $date = $attendance->start_at <= $attendance->end_at ? $attendance->date : date('Y-m-d', strtotime('-1 day', strtotime($attendance->date)));
                            if(strtotime($attendance->entry_at) >= strtotime($date.' '.$attendance->start_at) + 60) $late++;
                        }

                        // Set
                        $users[$key]->workhours[$key2]->present = $attendances->count();
                        $users[$key]->workhours[$key2]->late = $late;
                    }
                }
            }
        }

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/summary/attendance/index', [
            'groups' => $groups,
            'users' => $users,
            't1' => $t1,
            't2' => $t2,
			'status' => $status
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|null  $id
     * @return \Illuminate\Http\Response
     */
    public function detail(Request $request, $id = null)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get the user
        if(Auth::user()->role_id == role('super-admin')) {
            $user = User::findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('admin')) {
            $user = User::where('group_id','=',Auth::user()->group_id)->findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            $user = User::where('group_id','=',Auth::user()->group_id)->whereIn('office_id',Auth::user()->managed_offices()->pluck('office_id')->toArray())->findOrFail($id);
        }

        // Set default date
        $dt1 = date('m') > 1 ? date('Y-m-d', strtotime(date('Y').'-'.(date('m')-1).'-24')) : date('Y-m-d', strtotime((date('Y')-1).'-12-24'));
        $dt2 = date('Y-m-d', strtotime(date('Y').'-'.date('m').'-23'));

        // Set params
        $category = $request->query('category') != null ? $request->query('category') : 1;
        $workhour = $request->query('workhour') != null ? $request->query('workhour') : 0;
        $t1 = $request->query('t1') != null ? DateTimeExt::change($request->query('t1')) : $dt1;
        $t2 = $request->query('t2') != null ? DateTimeExt::change($request->query('t2')) : $dt2;

        // Get the work hours
        $workhours = WorkHour::where('group_id','=',$user->group_id)->where('office_id','=',$user->office_id)->where('position_id','=',$user->position_id)->orderBy('name','asc')->get();

        // Get attendances
        if($workhour == 0)
            $attendances = Attendance::where('user_id','=',$user->id)->whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->orderBy('date','desc')->get();
        else
            $attendances = Attendance::where('user_id','=',$user->id)->where('workhour_id','=',$workhour)->whereDate('date','>=',$t1)->whereDate('date','<=',$t2)->orderBy('date','desc')->get();

        // Count attendances
        $count[1] = $attendances->count();

        // Get late attendances
        $late = 0;
        foreach($attendances as $key=>$attendance) {
            $date = $attendance->start_at <= $attendance->end_at ? $attendance->date : date('Y-m-d', strtotime('-1 day', strtotime($attendance->date)));
            if(strtotime($attendance->entry_at) >= strtotime($date.' '.$attendance->start_at) + 60) $late++;
            if($category == 2) if(strtotime($attendance->entry_at) < strtotime($date.' '.$attendance->start_at) + 60) $attendances->forget($key);
        }

        // Count late attendances
        $count[2] = $late;

        // Get absents
        $absents1 = Absent::where('user_id','=',$user->id)->where('category_id','=',1)->where('date','>=',$t1)->where('date','<=',$t2)->orderBy('date','desc')->get();
        $absents2 = Absent::where('user_id','=',$user->id)->where('category_id','=',2)->where('date','>=',$t1)->where('date','<=',$t2)->orderBy('date','desc')->get();
        if($category == 3) $attendances = $absents1;
        if($category == 4) $attendances = $absents2;

        // Get leaves
        $leaves = Leave::where('user_id','=',$user->id)->where('date','>=',$t1)->where('date','<=',$t2)->orderBy('date','desc')->get();
        if($category == 5) $attendances = $leaves;

        // Count absents
        $count[3] = count($absents1);
        $count[4] = count($absents2);
        $count[5] = count($leaves);

        // View
        return view('admin/summary/attendance/detail', [
            'user' => $user,
            'workhours' => $workhours,
            'attendances' => $attendances,
            'category' => $category,
            't1' => $t1,
            't2' => $t2,
            'count' => $count
        ]);
    }

    /**
     * Monitor the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function monitor(Request $request)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get the month and year
        $month = $request->query('month') ?: date('m');
        $year = $request->query('year') ?: date('Y');

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // Get work hours and group
        if(Auth::user()->role_id == role('super-admin')) {
            $work_hours = WorkHour::where('group_id','=',$request->query('group'))->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
            $group = Group::find($request->query('group'));
        }
        elseif(Auth::user()->role_id == role('admin')) {
            $work_hours = WorkHour::where('group_id','=',Auth::user()->group_id)->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
            $group = Group::find(Auth::user()->group_id);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            $work_hours = WorkHour::where('group_id','=',Auth::user()->group_id)->where('office_id','=',$request->query('office'))->whereIn('office_id',Auth::user()->managed_offices()->pluck('office_id')->toArray())->where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
            $group = Group::find(Auth::user()->group_id);
        }

        // Set dates
        $dates = [];
        if($group) {
            $dt1 = $month > 1 ? date('Y-m-d', strtotime($year.'-'.($month-1).'-'.$group->period_start)) : date('Y-m-d', strtotime(($year-1).'-12-'.$group->period_start));
            $dt2 = date('Y-m-d', strtotime($year.'-'.$month.'-'.$group->period_end));
            $d = $dt1;
            while(date('d/m/Y', strtotime("-1 day", strtotime($d))) != date('d/m/Y', strtotime($dt2))) {
                array_push($dates, date('d/m/Y', strtotime($d)));
                $d = date('Y-m-d', strtotime("+1 day", strtotime($d)));
            }
        }

        // View
        return view('admin/summary/attendance/monitor', [
            'month' => $month,
            'year' => $year,
            'groups' => $groups,
            'work_hours' => $work_hours,
            'dates' => $dates,
        ]);
    }

    public function exportSummaryAttendance(Request $request)
    {        // Set params
        $dt1 = date('m') > 1 ? date('Y-m-d', strtotime(date('Y').'-'.(date('m')-1).'-24')) : date('Y-m-d', strtotime((date('Y')-1).'-12-24'));
        $dt2 = date('Y-m-d', strtotime(date('Y').'-'.date('m').'-23'));
        $t1 = $request->query('t1') != null ? DateTimeExt::change($request->query('t1')) : $dt1;
        $t2 = $request->query('t2') != null ? DateTimeExt::change($request->query('t2')) : $dt2;
		
		// Set the status and status sign
            $status = $request->query('status') != null ? $request->query('status') : 1;
            $statusSign = $status == 1 ? '=' : '!=';
            $group = Auth::user()->group_id;
            $office = $request->office_id ;
            $position = $request->position_id ;

        // Get users
            if($office != 0 && $position != 0){
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('office_id','=',$office)->where('position_id','=',$position)
                ->where('end_date',$statusSign,null)->get();
            }
       
            else if($office != 0 && $position == 0){
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('office_id','=',$office)
                ->where('end_date',$statusSign,null)->get();
            }
       
            else if($office == 0 && $position != 0){
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('position_id','=',$position)
                ->where('end_date',$statusSign,null)->get();
            }
            else{
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$group)->where('end_date',$statusSign,null)->get();
            }

            //count
            if(count($users) > 0) {
                foreach($users as $key=>$user) {
                    // Set absents
                    $users[$key]->absent1 = Absent::where('user_id','=',$user->id)->where('category_id','=',1)->where('date','>=',$t1)->where('date','<=',$t2)->count();
                    $users[$key]->absent2 = Absent::where('user_id','=',$user->id)->where('category_id','=',2)->where('date','>=',$t1)->where('date','<=',$t2)->count();

                    // Set leaves
                    $users[$key]->leave = Leave::where('user_id','=',$user->id)->where('date','>=',$t1)->where('date','<=',$t2)->count();

                    // Get the work hours
                    $users[$key]->workhours = WorkHour::where('group_id','=',$user->group_id)->where('office_id','=',$user->office_id)->where('position_id','=',$user->position_id)->orderBy('name','asc')->get();

                    if(count($users[$key]->workhours) > 0) {
                        foreach($users[$key]->workhours as $key2=>$workhour) {
                            // Get attendances
                            $attendances = Attendance::where('user_id','=',$user->id)->where('workhour_id','=',$workhour->id)->where('date','>=',$t1)->where('date','<=',$t2)->get();

                            // Count late
                            $late = 0;
                            foreach($attendances as $attendance) {
                                $date = $attendance->start_at <= $attendance->end_at ? $attendance->date : date('Y-m-d', strtotime('-1 day', strtotime($attendance->date)));
                                if(strtotime($attendance->entry_at) >= strtotime($date.' '.$attendance->start_at) + 60) $late++;
                            }

                            // Set
                            $users[$key]->workhours[$key2]->present = $attendances->count();
                            $users[$key]->workhours[$key2]->late = $late;
                        }
                    }
                }
            }
        

        return Excel::download(new SummaryAttendanceExport($users), 'Rangkuman Absensi.xlsx');
           
    }

    public function ExportMonitorAttendance(Request $request)
    {
        $position_id = $request->position_id;  
        $office_id = $request->office_id;  
        $group_id = $request->group_id;  
        $year = $request->year;  
        $month = $request->month;  
        $month = $request->query('month') ?: date('m');
        $year = $request->query('year') ?: date('Y');

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // Get work hours and group
        if(Auth::user()->role_id == role('super-admin')) {
            $work_hours = WorkHour::where('group_id','=',$request->query('group'))->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->orderBy('name','asc')->get();
            $group = Group::find($request->query('group'));
        }
        elseif(Auth::user()->role_id == role('admin')) {
            $work_hours = WorkHour::where('group_id','=',Auth::user()->group_id)->where('office_id','=',$office_id)->where('position_id','=',$position_id)->orderBy('name','asc')->get();
            $group = Group::find(Auth::user()->group_id);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            $work_hours = WorkHour::where('group_id','=',Auth::user()->group_id)->where('office_id','=',$office_id)->whereIn('office_id',Auth::user()->managed_offices()->pluck('office_id')->toArray())->where('position_id','=',$position_id)->orderBy('name','asc')->get();
            $group = Group::find(Auth::user()->group_id);
        }

        $cek = Attendance::where('workhour_id','=',$work_hours[0]->id)->get();
        // foreach($cek as $attend) {
        //     $cek['year'] = date('Y', strtotime($attend->date));;
        // }
        for($i=0;$i<count($cek);$i++){
            $cek[$i]->year = date('Y', strtotime($cek[$i]->date));
            $cek[$i]->month = date('M', strtotime($cek[$i]->date));
        }
        // $y = date('Y', strtotime($cek[622]->date));
        $cek_year = $cek->where('year','=', 2023);
        dd($cek_year[550]);
        // Jan, Feb, Mar, Apr, May, Jun. Jul, Aug, Sep, Oct, Nov, Dec
    }

    // public static convert_month($month){
    //     if($month = "Jan") return 1;
    //     else if($month = "Feb") return 2;
    //     else if($month = "Mar") return 3;
    //     else if($month = "Apr") return 4;
    //     else if($month = "Feb") return 5;
    //     else if($month = "Feb") return 6;
    //     else if($month = "Feb") return 7;
    //     else if($month = "Feb") return 8;
    // }
}
