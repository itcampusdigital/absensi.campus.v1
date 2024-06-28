<?php

namespace App\Http\Controllers\APIMagang;

use App\Models\Group;
use App\Models\Divisi;
use App\Models\ReportDaily;
use Illuminate\Http\Request;
use App\Models\JabatanAttribute;
use App\Http\Controllers\Controller;

class APIMagangController extends Controller
{
    public function divisi()
    {
        $divisi = Divisi::all();
        foreach($divisi as $d){
            $d->tugas = json_decode($d->tugas);
        }
        return $divisi;
    }
    public function dailies()
    {
        $daily = ReportDaily::all();
        foreach($daily as $d){
            $d->report = json_decode($d->report);
        } 
        return $daily;
    }
    public function jabatan_attr()
    {
        return JabatanAttribute::all(); 
    }
    public function group()
    {
        return Group::select('id','name','created_at','updated_at')->where('id',1)->get();
    }
}
