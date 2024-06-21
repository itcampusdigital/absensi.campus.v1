<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Ajifatur\Helpers\RouteExt;

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
