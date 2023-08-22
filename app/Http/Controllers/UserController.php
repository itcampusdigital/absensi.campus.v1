<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\Group;
use App\Models\Office;
use App\Models\Kontrak;
use Ajifatur\Helpers\Date;
use App\Models\Attendance;
use Ajifatur\Helpers\Salary;
use Illuminate\Http\Request;
use App\Models\Certification;
use App\Models\UserIndicator;
use App\Models\SalaryCategory;
use Illuminate\Validation\Rule;
use App\Models\WorkHourCategory;
use Ajifatur\Helpers\DateTimeExt;
use App\Models\UserCertification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            if($request->query('office') == null) {
                // Get users by the group
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$request->query('group'))->where('end_date','=',null)->orderBy('name','asc')->get();
            }
            else {
                // Get users by the group and office
                $users = User::where('role_id','=',role('member'))->where('group_id','=',$request->query('group'))->where('office_id','=',$request->query('office'))->where('end_date','=',null)->orderBy('name','asc')->get();
            }

            // Return
            return response()->json($users);
        }

        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Set the status and status sign
        $status = $request->query('status') != null ? $request->query('status') : 1;
        $statusSign = $status == 1 ? '=' : '!=';

        // Get users
        if(Auth::user()->role_id == role('super-admin')) {
            if($request->query('role') == 'admin')
                $users = User::where('role_id','=',role('admin'))->orderBy('last_visit','desc')->get();
            elseif($request->query('role') == 'manager')
                $users = User::where('role_id','=',role('manager'))->orderBy('last_visit','desc')->get();
            elseif($request->query('role') == 'member') {
                if($request->query('group') != null && $request->query('group') != 0) {
                    if($request->query('office') != null && $request->query('office') != 0 && $request->query('position') != null && $request->query('position') != 0)
                        $users = User::where('role_id','=',role('member'))->where('group_id','=',$request->query('group'))->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
                    elseif(($request->query('office') == null || $request->query('office') == 0) && $request->query('position') != null && $request->query('position') != 0)
                        $users = User::where('role_id','=',role('member'))->where('group_id','=',$request->query('group'))->where('position_id','=',$request->query('position'))->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
                    elseif($request->query('office') != null && $request->query('office') != 0 && ($request->query('position') == null || $request->query('position') == 0))
                        $users = User::where('role_id','=',role('member'))->where('group_id','=',$request->query('group'))->where('office_id','=',$request->query('office'))->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
                    else
                        $users = User::where('role_id','=',role('member'))->where('group_id','=',$request->query('group'))->where('end_date',$statusSign,null)->orderBy('name','asc')->get();

                }
                else
                    $users = User::where('role_id','=',role('member'))->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
            }
            else
                return redirect()->route('admin.user.index', ['role' => 'member']);
        }
        elseif(Auth::user()->role_id == role('admin')) {
            if($request->query('role') == 'admin')
                $users = User::where('role_id','=',role('admin'))->where('group_id','=',Auth::user()->group_id)->orderBy('last_visit','desc')->get();
            elseif($request->query('role') == 'manager')
                $users = User::where('role_id','=',role('manager'))->where('group_id','=',Auth::user()->group_id)->orderBy('last_visit','desc')->get();
            elseif($request->query('role') == 'member') {
                if($request->query('office') != null && $request->query('office') != 0 && $request->query('position') != null && $request->query('position') != 0)
                    $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('office_id','=',$request->query('office'))->where('position_id','=',$request->query('position'))->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
                elseif(($request->query('office') == null || $request->query('office') == 0) && $request->query('position') != null && $request->query('position') != 0)
                    $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('position_id','=',$request->query('position'))->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
                elseif($request->query('office') != null && $request->query('office') != 0 && ($request->query('position') == null || $request->query('position') == 0))
                    $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('office_id','=',$request->query('office'))->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
                else
                    $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
            }
            else
                return redirect()->route('admin.user.index', ['role' => 'member']);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            if($request->query('role') == 'admin' || $request->query('role') == 'manager')
                abort(403);
            elseif($request->query('role') == 'member') {
                if($request->query('office') != null && $request->query('office') != 0 && $request->query('position') != null && $request->query('position') != 0)
                    $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('office_id','=',$request->query('office'))->whereIn('office_id',Auth::user()->managed_offices()->pluck('office_id')->toArray())->where('position_id','=',$request->query('position'))->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
                elseif(($request->query('office') == null || $request->query('office') == 0) && $request->query('position') != null && $request->query('position') != 0)
                    $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('position_id','=',$request->query('position'))->whereIn('office_id',Auth::user()->managed_offices()->pluck('office_id')->toArray())->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
                elseif($request->query('office') != null && $request->query('office') != 0 && ($request->query('position') == null || $request->query('position') == 0))
                    $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('office_id','=',$request->query('office'))->whereIn('office_id',Auth::user()->managed_offices()->pluck('office_id')->toArray())->where('end_date',$statusSign,null)->orderBy('name','asc')->get();
                else
                    $users = User::where('role_id','=',role('member'))->where('group_id','=',Auth::user()->group_id)->where('end_date',$statusSign,null)->whereIn('office_id',Auth::user()->managed_offices()->pluck('office_id')->toArray())->orderBy('name','asc')->get();
            }
            else
                return redirect()->route('admin.user.index', ['role' => 'member']);
        }

        // Get groups
        $groups = Group::orderBy('name','asc')->get();
        
        // View
        return view('admin/user/index', [
            'users' => $users,
            'groups' => $groups,
            'status' => $status
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
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get roles
        $roles = Role::where('code','!=','super-admin')->orderBy('num_order','asc')->get();

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/user/create', [
            'roles' => $roles,
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
        // Validation
        $validator = Validator::make($request->all(), [
            'group_id' => Auth::user()->role_id == role('super-admin') ? 'required' : '',
            'office_id' => $request->role_id == role('member') ? 'required' : '',
            'position_id' => $request->role_id == role('member') ? 'required' : '',
            'offices' => $request->role_id == role('manager') ? 'required' : '',
            'name' => 'required|max:200',
            'birthdate' => $request->role_id == role('member') ? 'required' : '',
            'gender' => $request->role_id == role('member') ? 'required' : '',
            'address' => $request->role_id == role('member') ? 'required' : '',
            'start_date' => $request->role_id == role('member') ? 'required' : '',
            'phone_number' => $request->role_id == role('member') ? 'required|numeric' : '',
            'email' => 'required|email|unique:users',
            'username' => 'required|alpha_dash|min:4|unique:users',
            'password' => 'required|min:6',
			
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the user
            $user = new User;
            $user->role_id = $request->role_id;
            $user->group_id = Auth::user()->role_id == role('super-admin') ? $request->group_id : Auth::user()->group_id;
            $user->office_id = $request->role_id == role('member') ? $request->office_id : 0;
            $user->position_id = $request->role_id == role('member') ? $request->position_id : 0;
            $user->name = $request->name;
            $user->birthdate = $request->role_id == role('member') ? DateTimeExt::change($request->birthdate) : null;
            $user->gender = $request->role_id == role('member') ? $request->gender : '';
            $user->address = $request->role_id == role('member') ? $request->address : '';
			$user->note = $request->role_id == role('member') ? $request->note : null;
            $user->start_date = $request->role_id == role('member') ? DateTimeExt::change($request->start_date) : null;
            $user->end_date = $request->end_date != '' ? DateTimeExt::change($request->end_date) : null;
            $user->phone_number = $request->role_id == role('member') ? $request->phone_number : '';
            $user->identity_number = $request->role_id == role('member') ? $request->identity_number : '';
            $user->latest_education = $request->latest_education;
            $user->email = $request->email;
            $user->username = $request->username;
            $user->password = bcrypt($request->password);
            $user->status = 1;
            $user->last_visit = null;
            $user->save();

            //kontrak user baru
            $new_kontrak = new Kontrak;
            $d = strval($request->masa);
            $change_start_kontrak = DateTimeExt::change($request->start_date_kontrak);

            $new_kontrak->user_id =  $user->id;
            $new_kontrak->start_date_kontrak =  $change_start_kontrak;
            $new_kontrak->masa = $request->masa;
            $new_kontrak->end_date_kontrak = date('Y-m-d', strtotime( $change_start_kontrak.'+'.$d.' month'));
            $new_kontrak->save();

            

            // If manager, attach offices
            if($user->role_id == role('manager')) {
                $user->managed_offices()->attach($request->offices);
            }

            // Get the role
            $role = Role::find($user->role_id);

            // Redirect
            return redirect()->route('admin.user.index', ['role' => $role->code])->with(['message' => 'Berhasil menambah data.']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get the user
        if(Auth::user()->role_id == role('super-admin')) {
            $user = User::findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('admin')) {
            $user = User::with('kontrak')
            ->whereHas('kontrak', function($query){
                return $query->where('user_id','$id');
            })
            ->where('group_id','=',Auth::user()->group_id)->findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            $user = User::with('kontrak')->where('group_id','=',Auth::user()->group_id)->whereIn('office_id',Auth::user()->managed_offices()->pluck('office_id')->toArray())->findOrFail($id);
        }

        // View
        return view('admin/user/detail', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        
        // Get roles
        $roles = Role::where('code','!=','super-admin')->orderBy('num_order','asc')->get();

        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/user/edit', [
            'user' => $user,
            'roles' => $roles,
            'groups' => $groups,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'office_id' => $request->role_id == role('member') ? 'required' : '',
            'position_id' => $request->role_id == role('member') ? 'required' : '',
            'offices' => $request->role_id == role('manager') ? 'required' : '',
            'name' => 'required|max:200',
            'birthdate' => $request->role_id == role('member') ? 'required' : '',
            'gender' => $request->role_id == role('member') ? 'required' : '',
            'phone_number' => $request->role_id == role('member') ? 'required|numeric' : '',
            'email' => [
                'required', 'email', Rule::unique('users')->ignore($request->id, 'id')
            ],
            'username' => [
                'required', 'alpha_dash', 'min:4', Rule::unique('users')->ignore($request->id, 'id')
            ],
            'password' => $request->password != '' ? 'min:6' : '',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Update the user
            $user = User::find($request->id);
            $user->office_id = $request->office_id;
            $user->position_id = $request->position_id;
            $user->name = $request->name;
            $user->birthdate = $request->role_id == role('member') ? DateTimeExt::change($request->birthdate) : null;
            $user->gender = $request->role_id == role('member') ? $request->gender : '';
			$user->note = $request->role_id == role('member') ? $request->note : '';
            $user->address = $request->role_id == role('member') ? $request->address : '';
            $user->start_date = $request->role_id == role('member') ? DateTimeExt::change($request->start_date) : null;
            $user->end_date = $request->end_date != '' ? DateTimeExt::change($request->end_date) : null;
            $user->phone_number = $request->role_id == role('member') ? $request->phone_number : '';
            $user->latest_education = $request->latest_education;
            $user->identity_number = $request->role_id == role('member') ? $request->identity_number : '';
            $user->email = $request->email;
            $user->username = $request->username;
            $user->password = $request->password != '' ? bcrypt($request->password) : $user->password;
            $user->save();

            //kontrak
            if($user->kontrak == null){
                $d = strval($request->masa);

                $kontrak = new Kontrak;
                $kontrak->user_id = $request->id;
                $kontrak->masa = $request->masa;
                $kontrak->start_date_kontrak = $request->start_date_kontrak != null ? $request->start_date_kontrak : null;
                
                $kontrak->end_date_kontrak = date('Y-m-d', strtotime($request->start_date_kontrak.'+'.$d.' month'));
                $kontrak->save();
            }
            else{
                // $Date = "2023-08-22";
                $d = strval($request->masa);
                $request->start_date_kontrak = DateTimeExt::change($request->start_date_kontrak);
                
                $end_date= date('Y-m-d', strtotime($request->start_date_kontrak.'+'.$d.' month'));
                // $user->kontrak->masa = $request->masa;
                // $user->kontrak->start_date_kontrak = DateTimeExt::change($request->start_date_kontrak);
                // $user->kontrak->save();
                DB::table('kontrak')->where('user_id',$user->kontrak->user_id)
                            ->update(['masa'=> $request->masa]);
                DB::table('kontrak')->where('user_id',$user->kontrak->user_id)
                            ->update(['start_date_kontrak'=> $request->start_date_kontrak]);
                DB::table('kontrak')->where('user_id',$user->kontrak->user_id)
                            ->update(['end_date_kontrak'=> $end_date]);
                              
                
            }

            // If manager, sync offices
            if($user->role_id == role('manager')) {
                $user->managed_offices()->sync($request->offices);
            }

            // Get the role
            $role = Role::find($user->role_id);

            // Redirect
            return redirect()->route('admin.user.index', ['role' => $role->code])->with(['message' => 'Berhasil mengupdate data.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);

        // Get the user
        $user = User::find($request->id);

        // Delete the user
        $user->delete();

        // Get the role
        $role = Role::find($user->role_id);

        // Redirect
        return redirect()->route('admin.user.index', ['role' => $role->code])->with(['message' => 'Berhasil menghapus data.']);
    }

    /**
     * Edit the user certifications.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editCertification($id)
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

        // Get the certifications
        $certifications = Certification::where('position_id','=',$user->position_id)->orderBy('name','asc')->get();

        // View
        return view('admin/user/edit-certification', [
            'user' => $user,
            'certifications' => $certifications
        ]);
    }

    /**
     * Update the user certifications.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCertification(Request $request)
    {
        // Get the user
        $user = User::find($request->id);

        // Save or update certifications
        foreach($request->dates as $key=>$date) {
            $uc = UserCertification::find($request->ids[$key]);
            if($date != null) {
                if(!$uc) $uc = new UserCertification;

                $uc->user_id = $user->id;
                $uc->certification_id = $request->certifications[$key];
                $uc->date = DateTimeExt::change($date);
                $uc->save();
            }
            else {
                if($uc) $uc->delete();
            }
        }

        // Redirect
        return redirect()->route('admin.user.edit-certification', ['id' => $user->id])->with(['message' => 'Berhasil mengupdate data.']);
    }
}
