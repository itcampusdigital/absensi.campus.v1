<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Lembur;
use App\Models\Kontrak;
use App\Models\WorkHour;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(Auth::user()->role->code == 'super-admin' || Auth::user()->role->code == 'admin' ) {
            //kontrak
            $kontrak = Kontrak::with('user')->whereHas('user', function($query){
                                        return $query->whereNull('end_date');
                                    })->get();

            $kontrak_count = $kontrak->count();
            //lemburan
            $lembur = Lembur::where('status','=',3)->get();
            $lembur_count = $lembur->count();

            //magang
            $magang_count = User::where('role_id','=',3)->where('group_id','=',1)
                        ->where('office_id','=',19)
                        ->where('end_date','=',null)->count();

            //all karyawan
            $users_count = User::where('role_id','=',3)->where('group_id','=',1)
                            ->where('end_date','=',null)->count();

            $data_all = array();
            
            for($i = 0; $i < $kontrak_count; $i++){
                $conv_format = date('Y/m/d',strtotime($kontrak[$i]->end_date_kontrak));
                $selisih = Carbon::parse(date('Y/m/d', time()))->diffInDays(Carbon::parse($conv_format),false);
                if($selisih <= 7 && $selisih >= 0){
                    $data_all[$i] = $kontrak[$i];
                }
            }

            // for($i = 0; $i < $lembur_count; $i++){
            //     $data_all[$i + $kontrak_count] = $lembur[$i];
            // }
            
            // View
            return view('admin/dashboard/adminIndex',[
                'notif_kontrak' => count($data_all),
                'kontrak' => $kontrak,
                'new_data' => $lembur, 
                'kontrak_count' => $kontrak_count,
                'users_count' => $users_count,
                'lembur_count' => $lembur_count,
                'magang_count' => $magang_count
            ]);
        }
        else if (Auth::user()->role->code == 'manager'){
            return view('admin/dashboard/index');
        }
        elseif(Auth::user()->role->code == 'member') {
            // Check whether is already absent and not exit yet
            $is_entry = Attendance::has('workhour')->where('user_id','=',Auth::user()->id)->where('exit_at','=',null)->whereIn('date',[date('Y-m-d'), date('Y-m-d',strtotime("+1 day"))])->get();

            // Display attendance if is already exit
            if(count($is_entry) <= 0) {
                // Get work hours
		        $work_hours = WorkHour::where('group_id','=',Auth::user()->group_id)->where('office_id','=',Auth::user()->office_id)->where('position_id','=',Auth::user()->position_id)->get();

                if(count($work_hours) > 0) {
                    foreach($work_hours as $key=>$work_hour) {
                        // Get the entry time
                        $entry_at = date('Y-m-d H:i:s');

                        // If start_at and end_at are still at the same day
                        if(strtotime($work_hour->start_at) <= strtotime($work_hour->end_at)) {
                            $start_date = date('Y-m-d', strtotime($entry_at));
                            $end_date = date('Y-m-d', strtotime($entry_at));
                        }
                        // If start_at and end_at are at the different day
                        else {
                            // If the user login at 1 hour before work time
                            if(date('G', strtotime($entry_at)) >= (date('G', strtotime($work_hour->start_at)) - 1)) {
                                $start_date = date('Y-m-d', strtotime($entry_at));
                                $end_date = date('Y-m-d', strtotime("+1 day"));
                            }
                            // If the user login at 1 hour after work time
                            elseif(date('G', strtotime($entry_at)) < (date('G', strtotime($work_hour->end_at)) + 1)) {
                                $start_date = date('Y-m-d', strtotime("-1 day"));
                                $end_date = date('Y-m-d', strtotime($entry_at));
                            }
							else {
								$start_date = date('Y-m-d', strtotime($entry_at));
								$end_date = date('Y-m-d', strtotime($entry_at));
							}
                        }

                        // Set start time and end time
                        $start_time = new \DateTime($start_date.' '.$work_hour->start_at);
                        $end_time = new \DateTime($end_date.' '.$work_hour->end_at);

                        // Set the attendance status
                        if(strtotime($entry_at) >= (strtotime($start_time->format('Y-m-d H:i:s')) - 3600) && strtotime($entry_at) <= (strtotime($end_time->format('Y-m-d H:i:s')) + 3600))
                            $work_hours[$key]->status = 1;
                        else
                            $work_hours[$key]->status = 0;
                    }
                }
            }
            else{
                // Display attendance (exit)
                $work_hours = $is_entry;
            }

            // View
            return view('member/index', [
                'is_entry' => $is_entry,
                'work_hours' => $work_hours,
            ]);
        }
        else{
            abort(404);
        }
    }
}
