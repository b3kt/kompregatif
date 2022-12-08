<?php

use App\Http\Controllers\SSOController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome');
    return redirect('/admin/login');
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/admin/login');
});

Route::group(['prefix' => 'auth'], function () {

    Route::get('redirect',  [ 'uses'=>'App\Http\Controllers\SSOController@redirect', 'as'=>'sso.redirect']);
    Route::get('callback',  [ 'uses'=>'App\Http\Controllers\SSOController@callback', 'as'=>'sso.callback']);
    Route::get('handle',  [ 'uses'=>'App\Http\Controllers\SSOController@handle', 'as'=>'sso.handle']);
    Route::get('test',  [ 'uses'=>'App\Http\Controllers\SSOController@test', 'as'=>'sso.test']);

});



Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();

    Route::get('app-kelas/relation', [ 'uses'=>'App\Http\Controllers\OptionsController@relation', 'as'=>'voyager.app-kelas.relation']);
    Route::get('app-kelas/{id}', [ 'uses'=>'App\Http\Controllers\AppKelasController@show', 'as'=>'voyager.app-kelas.show']);

    // Your overwrites here
    // Route::get('bpm-app-instances', [BpmAppInstanceController::class, 'index']);
    // Route::get('bpm-app-instances/fill/{id}', [ 'uses'=>'App\Http\Controllers\BpmAppInstanceController@fill', 'as'=>'voyager.bpm-app-instances.fill']);

    // Route::get('assets/{id}', [ 'uses'=>'App\Http\Controllers\AssetsController@show', 'as'=>'voyager.assets.show']);
    // Route::post('assets', [ 'uses'=>'App\Http\Controllers\AssetsController@store', 'as'=>'voyager.assets.store']);

    // Route::get('app-reval-ihpb/relation', [ 'uses'=>'App\Http\Controllers\AppRevaluasiController@relation', 'as'=>'voyager.app-reval-ihpb.relation']);
    // Route::get('app-reval-ppi/relation', [ 'uses'=>'App\Http\Controllers\AppRevaluasiController@relation', 'as'=>'voyager.app-reval-ppi.relation']);
    // Route::get('app-reval-ikk/relation', [ 'uses'=>'App\Http\Controllers\AppRevaluasiController@relation', 'as'=>'voyager.app-reval-ikk.relation']);
    // Route::get('app-reval-njop/relation', [ 'uses'=>'App\Http\Controllers\AppRevaluasiController@relation', 'as'=>'voyager.app-reval-njop.relation']);
    // Route::get('app-reval-rcn/relation', [ 'uses'=>'App\Http\Controllers\AppRevaluasiController@relation', 'as'=>'voyager.app-reval-rcn.relation']);
    // Route::get('bpm-master-tasks/relation', [ 'uses'=>'App\Http\Controllers\AppRevaluasiController@relation', 'as'=>'voyager.bpm-master-tasks.relation']);
    // Route::get('assets/relation', [ 'uses'=>'App\Http\Controllers\AppRevaluasiController@relation', 'as'=>'voyager.assets.relation']);

    // Route::get('app-revaluasi-assets/relation', [ 'uses'=>'App\Http\Controllers\AppRevaluasiController@relation', 'as'=>'voyager.app-revaluasi-assets.relation']);
    // Route::get('summary-revaluasi-assets/{tahun}', [ 'uses'=>'App\Http\Controllers\AppRevaluasiController@summary', 'as'=>'voyager.app-revaluasi-assets.summary']);

    // Route::get('koefisien-revaluasi/relation', [ 'uses'=>'App\Http\Controllers\AppRevaluasiController@relation', 'as'=>'voyager.koefisien-revaluasi.relation']);

    // Route::post('flow-usulan-investasi/{id}', [ 'uses'=>'App\Http\Controllers\FlowUsulanInvestasiController@update', 'as'=>'voyager.flow-usulan-investasi.update']);

    // // import
    // Route::get('import',['uses'=>'App\Http\Controllers\ImportController@form', 'as'=>'import.form']);
    // Route::post('import/parse', ['uses'=>'App\Http\Controllers\ImportController@parseImport', 'as'=>'import.parse']);
    // Route::post('import/process', ['uses'=>'App\Http\Controllers\ImportController@processImport', 'as'=>'import.process']);

    // Route::get('summary-revaluasi-assets/{tahun}/export/{type}',[ 'uses'=>'App\Http\Controllers\AppRevaluasiController@export', 'as'=>'voyager.app-revaluasi-assets.export']);

    // Route::get('roles/{id}/edit',['uses'=>'App\Http\Controllers\Voyager\VoyagerRoleController@edit', 'as'=>'voyager.roles.edit']);
    // Route::put('roles/{id}',['uses'=>'App\Http\Controllers\Voyager\VoyagerRoleController@update', 'as'=>'voyager.roles.update']);

    // Route::get('admin/media',['uses'=>'App\Http\Controllers\Voyager\VoyagerMediaController@index', 'as'=>'voyager.media.index']);
    // Route::get('/',['uses'=>'App\Http\Controllers\Voyager\VoyagerController@index', 'as'=>'voyager.dashboard']);

    // Route::get('settings/update_base_year/{value}',['uses'=>'App\Http\Controllers\Voyager\VoyagerSettingsController@update_value', 'as'=>'voyager.settings.update_value']);

});



