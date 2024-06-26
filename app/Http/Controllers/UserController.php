<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Role;
use App\Models\User;
use App\Models\Group;
use App\Models\Divisi;
use App\Models\Office;
use App\Models\Kontrak;
use App\Models\Position;
use Ajifatur\Helpers\Date;
use App\Models\Attendance;
use App\Exports\ExportUser;
use Ajifatur\Helpers\Salary;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Models\Certification;
use App\Models\UserIndicator;
use App\Models\WorkHourTugas;
use App\Models\SalaryCategory;
use Illuminate\Validation\Rule;
use App\Models\JabatanAttribute;
use App\Models\WorkHourCategory;
use Ajifatur\Helpers\DateTimeExt;
use App\Models\UserCertification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
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
        $divisi = Divisi::select('id','name')->get();

        // Get roles
        $roles = Role::where('code','!=','super-admin')->orderBy('num_order','asc')->get();
        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/user/create', [
            'roles' => $roles,
            'groups' => $groups,
            'divisi'=>$divisi
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

            //divisi
            $divisi_new = new JabatanAttribute;
            $divisi_new->user_id = $user->id;
            $divisi_new->division_id = $request->divisi;
            $divisi_new->save();


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
        $divisi = Divisi::select('id','name')->get();
        // Get the user
        if(Auth::user()->role_id == role('super-admin')) {
            $user = User::findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('admin')) {
            // $user = User::with('kontrak')
            // ->whereHas('kontrak', function($query) use ($id) {
            //     return $query->where('user_id',$id);
            // })
            // ->where('group_id','=',Auth::user()->group_id)->findOrFail($id);
            $user = User::findOrFail($id);
        }
        elseif(Auth::user()->role_id == role('manager')) {
            $user = User::with('kontrak')->where('group_id','=',Auth::user()->group_id)->whereIn('office_id',Auth::user()->managed_offices()->pluck('office_id')->toArray())->findOrFail($id);
        }

        // View
        return view('admin/user/detail', [
            'user' => $user,
            'divisi'=>$divisi

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

        $jabatan = JabatanAttribute::where('user_id',$id)->first();
        // Get roles
        $roles = Role::where('code','!=','super-admin')->orderBy('num_order','asc')->get();
        $divisi = Divisi::select('id','name')->get();
        // Get groups
        $groups = Group::orderBy('name','asc')->get();

        // View
        return view('admin/user/edit', [
            'user' => $user,
            'roles' => $roles,
            'groups' => $groups,
            'divisi'=>$divisi,
            'jabatan'=>$jabatan

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
            if($user->end_date != null){
                $user->status = 0;
            }
            else{
                $user->status = 1;
            }
            $user->phone_number = $request->role_id == role('member') ? $request->phone_number : '';
            $user->latest_education = $request->latest_education;
            $user->identity_number = $request->role_id == role('member') ? $request->identity_number : '';
            $user->email = $request->email;
            $user->username = $request->username;
            $user->password = $request->password != '' ? bcrypt($request->password) : $user->password;
            $user->save();

            //divisi
            if(isset($request->divisi)){
                $divisi = JabatanAttribute::where('user_id',$request->id)->first();
                if($divisi == null){
                    $divisi_new = new JabatanAttribute;
                    $divisi_new->user_id = $request->id;
                    $divisi_new->division_id = $request->divisi;
                    $divisi_new->save();
                }
                else{
                    $divisi->division_id = $request->divisi;
                    $divisi->save();
                }
            }


            //kontrak
            if($user->kontrak == null){
                $d = strval($request->masa);

                $kontrak = new Kontrak;
                $kontrak->user_id = $request->id;
                $kontrak->masa = $request->masa;

                $request->start_date_kontrak = DateTimeExt::change($request->start_date_kontrak);
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
        $kontrak = Kontrak::find($request->id);
        // Delete the user
        $user->delete();

        if($kontrak != null){
            $kontrak->delete();
        }
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

    public function exportKaryawan(Request $request){

        $position_id = $request->position_id;
        $office_id = $request->office_id;
        $statusSign = $request->status == 1 ? '=' : '!=';

        if($position_id){
            $userExport = User::with(['office', 'position'])
                        ->whereHas('position', function($query) use ($position_id){
                            return $query->where('position_id','=', $position_id);
                        })
                        ->where('role_id',3)
                        ->where('end_date',$statusSign,null)
                        ->get();
        }
        elseif($office_id){
            $userExport = User::with(['office', 'position'])
                        ->whereHas('office', function($query) use ($office_id){
                            return $query->where('office_id','=', $office_id);
                        })
                        ->where('role_id',3)
                        ->where('end_date',$statusSign,null)
                        ->get();
        }
        elseif($position_id && $office_id){
            $userExport = User::with(['office', 'position'])
                        ->whereHas('office', function($query) use ($office_id){
                            return $query->where('office_id','=', $office_id);
                        })
                        ->whereHas('position', function($query) use ($position_id){
                            return $query->where('position_id','=', $position_id);
                        })
                        ->where('role_id',3)
                        ->where('end_date',$statusSign,null)
                        ->get();
        }
        else{
            $userExport = User::with(['office', 'position'])
                        ->where('role_id',3)
                        ->where('end_date',$statusSign,null)
                        ->get();
        }
        return Excel::download(new ExportUser($userExport), 'users.xlsx');
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        $data = Excel::toArray(new UsersImport, $file);
        $rows = $data[0];
        for($i=0;$i<count($rows);$i++)
        {
            $row = $rows[$i];
            if($row[5] != null){
                $office_id = Office::select('id','name')->where('group_id',Auth::user()->group_id)->where('name','LIKE','%'.$row[1].'%')->pluck('id')->toArray();
                $position_id = Position::select('id','name')->where('group_id',Auth::user()->group_id)->where('name','LIKE','%'.$row[2].'%')->pluck('id')->toArray();
                $password = $row[17] != null ? $row[17] : 123456;
                //generated username
                $username = $row[16] != null ? $row[16] : strstr($row[14], '@', true).'0'.rand(0,100);
                $cek_username = User::select('username')->where('username',$username)->first();
                $cek_email = User::select('email')->where('email',$row[14])->first();

                if($cek_username != null || $cek_email != null){
                    return abort(400,'Username atau Email pada nama "'.$row[4].'" Sudah Terdaftar');
                }
                else if($office_id == null || $position_id == null){
                    return abort(400,'Posisi atau kantor pada nama "'.$row[4].'" Tidak Ditemukan');
                }
                else{
                    $user_newData = new User;
                    $user_newData->role_id = 3;
                    $user_newData->group_id = Auth::user()->group_id;
                    $user_newData->office_id = $office_id[0];
                    $user_newData->position_id = $position_id[0];
                    $user_newData->name = $row[4];
                    $user_newData->username = $username;
                    $user_newData->email = $row[14];
                    $user_newData->password = bcrypt($password);
                    $user_newData->remember_token = null;
                    $user_newData->avatar = null;
                    $user_newData->access_token = null;
                    $user_newData->birthdate = $row[5];
                    $user_newData->gender = $row[6];
                    $user_newData->phone_number = $row[15];
                    $user_newData->address = $row[7];
                    $user_newData->latest_education = $row[8];
                    $user_newData->identity_number = $row[9];
                    $user_newData->start_date = $row[10];
                    $user_newData->end_date=$row[13];
                    $user_newData->status = 1;
                    $user_newData->created_at = date('Y-m-d H:i:s');
                    $user_newData->note =$row[18];
                    $user_newData->save();


                    //kontrak user baru
                    $new_kontrak = new Kontrak;
                    $d = strval($row[12]);
                    $start_date_kontrak = $row[11] != null ? $row[11] : date('Y-m-d');
                    // $change_start_kontrak = DateTimeExt::change($start_date_kontrak);

                    $new_kontrak->user_id =  $user_newData->id;
                    $new_kontrak->start_date_kontrak =  $start_date_kontrak;

                    $new_kontrak->masa = $row[12];
                    $new_kontrak->end_date_kontrak = date('Y-m-d', strtotime( $start_date_kontrak.'+'.$d.' month'));
                    $new_kontrak->save();

                    // //divisi
                    // $cek_divisi = Divisi::select('id','name')->where('name','LIKE','%'.$row[3].'%')->first();
                    // if($cek_divisi == null){
                    //     $divisi_baru = new Divisi;
                    //     $divisi_baru->group_id = Auth::user()->group_id;
                    //     $divisi_baru->name = $row[3];
                    //     $divisi_baru->save();

                    //     $cek_divisi = $divisi_baru;
                    // }

                    // $divisi_new = new JabatanAttribute;
                    // $divisi_new->user_id = $user_newData->id;
                    // $divisi_new->division_id = $cek_divisi->id;
                    // $divisi_new->save();
                }
            }

        }

        // Redirect
        return redirect()->route('admin.user.index', ['role' => 'member'])->with(['message' => 'Berhasil menambah data.']);
    }
}
