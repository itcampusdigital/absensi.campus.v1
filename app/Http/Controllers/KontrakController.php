<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Kontrak;
use Illuminate\Http\Request;
use Ajifatur\Helpers\DateTimeExt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KontrakController extends Controller
{
    public function index()
    {
        // Check the access
        has_access(method(__METHOD__), Auth::user()->role_id);
        $user = Kontrak::with('user')->get();

        return view('admin.kontrak.index', [
            'users' => $user,
        ]);
    }

    public function edit(Request $request)
    {
        has_access(method(__METHOD__), Auth::user()->role_id);
        $user_selected = User::with('kontrak')->find($request->id);

        // If manager, sync offices
        if($user_selected->role_id == role('manager')) {
            $user_selected->managed_offices()->sync($request->offices);
        }

        // Get the role
        $role = Role::find($user_selected->role_id);

        return view('admin.kontrak.edit', [
            'user_select' => $user_selected,
        ]);
    }
    public function update(Request $request)
    {
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
