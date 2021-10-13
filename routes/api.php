<?php

use Illuminate\Support\Facades\Route;

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

Route::middleware(['cors'])->group(function () {
    // Users
    Route::get('/users', 'UserController@index');
    Route::get('/users/{id}', 'UserController@show');
    Route::post('/users', 'UserController@store');
    Route::get('/users/w_producer/{id}', 'WProducerController@users');
    Route::put('/users/{id}', 'UserController@update')->middleware('horeca.auth');
    Route::delete('/users/{id}', 'UserController@destroy')->middleware('horeca.auth');
    Route::post('/login', 'UserController@login');
    Route::post('/logout', 'UserController@logout')->middleware('horeca.auth');
    Route::get('/users/{id}/heatmaps', 'UserController@heatmaps')->middleware('horeca.auth_admin');
    Route::get('/users/{id}/routes', 'UserController@routes')->middleware('horeca.auth_admin');
    Route::get('/users/{id}/reports', 'UserController@reports')->middleware('horeca.auth_admin');

    // Bins
    Route::get('/bins', 'BinController@index');
    Route::get('/bins/{id}', 'BinController@show');
    Route::get('/bins/reports/{id}', 'BinController@reports');
    Route::post('/bins', 'BinController@store')->middleware('horeca.auth_admin');
    Route::put('/bins/{id}', 'BinController@update')->middleware('horeca.auth_w_prod');
    Route::delete('/bins/{id}', 'BinController@destroy')->middleware('horeca.auth_admin');
    Route::get('/bins/w_producer/{id}', 'WProducerController@bins')->middleware('horeca.auth_w_prod');
    Route::post('/bins/closest', 'BinController@closest');

    // Reports
    Route::get('/reports', 'ReportController@index');
    Route::get('/reports/{id}', 'ReportController@show');
    Route::get('/reports/w_producer/{id}', 'ReportController@w_producer_reports');
    Route::post('/reports', 'ReportController@store')->middleware('horeca.auth');
    Route::put('/reports/{id}', 'ReportController@update')->middleware('horeca.auth_admin');
    Route::put('/reports/approve/{id}', 'ReportController@approve_report')->middleware('horeca.auth_admin');
    Route::delete('/reports/{id}', 'ReportController@destroy')->middleware('horeca.auth_admin');
    Route::delete('/reports/{id}/photos', 'ReportController@delete_photo')->middleware('horeca.auth_admin');

    // Heatmaps
    Route::get('/heatmaps', 'HeatmapController@index')->middleware('horeca.auth_admin');
    Route::get('/heatmaps/{id}', 'HeatmapController@show')->middleware('horeca.auth_admin');
    Route::post('/heatmaps', 'HeatmapController@store')->middleware('horeca.auth_admin');
    Route::put('/heatmaps/{id}', 'HeatmapController@update')->middleware('horeca.auth_admin');
    Route::delete('/heatmaps/{id}', 'HeatmapController@destroy')->middleware('horeca.auth_admin');

    // Routes
    Route::get('/routes', 'RouteController@index');
    Route::get('/routes/{id}', 'RouteController@show');
    Route::post('/routes', 'RouteController@store')->middleware('horeca.auth_admin');
    Route::put('/routes/{id}', 'RouteController@update');
    Route::delete('/routes/{id}', 'RouteController@destroy')->middleware('horeca.auth_admin');
    Route::post('/new_route', 'VehicleRouteController@store')->middleware('horeca.auth_admin');
    Route::get('/vehicle_route/{id}', 'VehicleRouteController@show');
    Route::put('/vehicle_route/{id}', 'VehicleRouteController@update');
    Route::get('/routes/vehicle/{id}', 'VehicleRouteController@vehicle_route');
    Route::post('/routes/completed/{id}', 'VehicleRouteController@conclude');

    // Vehicles
    Route::get('/vehicles', 'VehicleController@index');
    Route::get('/vehicles/{id}', 'VehicleController@show');
    Route::post('/vehicles', 'VehicleController@store')->middleware('horeca.auth_admin');
    Route::put('/vehicles/{id}', 'VehicleController@update')->middleware('horeca.auth_admin');
    Route::delete('/vehicles/{id}', 'VehicleController@destroy')->middleware('horeca.auth_admin');
    Route::get('/vehicles/plate/{plate}', 'VehicleController@from_plate');

    // W_Producers
    Route::get('/w_producers', 'WProducerController@index');
    Route::get('/w_producers/{id}', 'WProducerController@show');
    Route::get('/w_producers/from_user_id/{id}', 'WProducerController@from_user_id');
    Route::post('/w_producers', 'WProducerController@store');
    Route::put('/w_producers/{id}', 'WProducerController@update')->middleware('horeca.auth_w_prod');
    Route::put('/w_producers/approve/{id}', 'WProducerController@approve_wprod')->middleware('horeca.auth_admin');
    Route::delete('/w_producers/{id}', 'WProducerController@destroy')->middleware('horeca.auth_w_prod');
    Route::post('/w_producers/employer', 'WProducerController@new_employer');
    Route::post('/w_producers/employee', 'WProducerController@new_employee');
    Route::post('/w_producers/new_bin', 'WProducerController@new_bin')->middleware('horeca.auth_w_prod');
    Route::delete('/w_producers/{id}/bins', 'WProducerController@destroy_bin')->middleware('horeca.auth_w_prod');

    // POIs
    Route::get('/pois', 'PoisController@index');
    Route::get('/pois/{id}', 'PoisController@show');
    Route::post('/pois', 'PoisController@store');
    Route::put('/pois/{id}', 'PoisController@update');
    Route::delete('/pois/{id}', 'PoisController@destroy');

    // Wastes
    // Route::get('/wastes', 'WasteController@index');
    // Route::get('/wastes/{id}', 'WasteController@show');
    // Route::post('/wastes', 'WasteController@store');
    // Route::put('/wastes/{id}', 'WasteController@update');
    // Route::delete('/wastes/{id}', 'WasteController@destroy');
});
