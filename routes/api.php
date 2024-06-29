<?php

use Illuminate\Http\Request;
use Ajifatur\Helpers\RouteExt;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIMagang\APIMagangController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
//magang
Route::get('/magang/divisi', [APIMagangController::class,'divisi'])->name('api.divisi');
Route::get('/magang/dailies',[APIMagangController::class,'dailies'])->name('api.dailies');
Route::get('/magang/jabatan_attr',[APIMagangController::class,'jabatan_attr'])->name('api.jabatan_attr');
Route::get('/magang/group',[APIMagangController::class,'group'])->name('api.group');
Route::get('/magang/user',[APIMagangController::class,'getUser'])->name('api.getUser');
//---


Route::get('/user', 'UserController@index')->name('api.user.index');
Route::get('/office', 'OfficeController@index')->name('api.office.index');
Route::get('/divisi', 'DivisiController@index')->name('api.divisi.index');
//DAP
Route::get('/userJobAll/{id}', 'JabatanAttributeController@apiJob')->name('api.apiJob,indexApi');
Route::get('/userJob', 'JabatanAttributeController@apiIndex')->name('api.getJob,indexApi');

Route::get('/userJob/report/{id}','ReportDailyController@rekapApi')->name('api.rekapApi,indexApi');
//--------
Route::get('/position', 'PositionController@index')->name('api.position.index');
Route::get('/work-hour', 'WorkHourController@index')->name('api.work-hour.index');
Route::get('/work-hour/divisi', 'WorkHourController@indexApi')->name('api.work-hour.indexApi');
Route::get('/certification', 'CertificationController@index')->name('api.certification.index');
Route::get('/salary-category', 'SalaryCategoryController@index')->name('api.salary-category.index');
//report

// FaturHelper Routes
RouteExt::api();
