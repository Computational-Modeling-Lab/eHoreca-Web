<?php

namespace App\Http\Controllers;

use App\Models\VehicleRoute;
use App\Models\Route;
use App\Models\Vehicle;
use Illuminate\Http\Request;

use App\Http\Resources\RouteResource;

class VehicleRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Route::post('/new_route', 'VehicleRouteController@store');
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request = $request->all();
        $validator = \Validator::make($request, array(
            'user_id' => 'required',
            'route_title' => 'required',
            'route_description' => 'required',
            'route_bins' => 'required',
            'vehicle_id' => 'required',
            'type' => 'required',
        ));

        if ($validator->fails()) {
            return response($validator->messages(), 400);
        }

        $toInsertRoute = [
            'title' => $request['route_title'],
            'description' => $request['route_description'],
            'bins' => is_array($request['route_bins']) ? json_encode($request['route_bins']) : $request['route_bins'],
            'user_id' => $request['user_id'],
        ];

        $toInsertVehicleRoute = [
            'vehicle_id' => $request['vehicle_id'],
            'route_id' => -1,
            'type' => $request['type']
        ];

        $route_id = Route::create($toInsertRoute)->id;

        if (!$route_id) {
            return \Helper::instance()->horeca_http_not_created();
        }

        $toInsertVehicleRoute['route_id'] = $route_id;

        if (!VehicleRoute::create($toInsertVehicleRoute)) {
            Route::find($route_id)->delete();
            return \Helper::instance()->horeca_http_not_created();
        }

        return \Helper::instance()->horeca_http_created();
    }

    /**
     * Display the specified resource.
     * Route::get('/vehicle_route/{id}', 'VehicleRouteController@show');
     *
     * @param  \App\Models\VehicleRoute  $vehicle_route
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $route = Route::find($id);
        $vehicle_route = VehicleRoute::where('route_id', $id)->first();
        $vehicle_id = $vehicle_route['vehicle_id'];

        return response(array('vehicle_id' => $vehicle_id, 'type' => $vehicle_route['type'], 'route' => $route));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VehicleRoute  $VehicleRoute
     * @return \Illuminate\Http\Response
     */
    public function edit(VehicleRoute $vehicle_route)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * Route::put('/new_route/{id}', 'VehicleRouteController@update');
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VehicleRoute  $vehicle_route
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $route = Route::find($id);
        $vehicle_route = VehicleRoute::where('route_id', $id)->first();
        $request = $request->all();

        $didUpdate = true;

        if (isset($request['targetVehicle']) || isset($request['type'])) {
            $vehicle_id = isset($request['targetVehicle']) ? $request['targetVehicle'] : null;
            $type = $request['type'] ? $request['type'] : null;
            $toInsert = [];

            if ($vehicle_id !== null) {
                $toInsert['vehicle_id'] = $vehicle_id;
                unset($request['targetVehicle']);
            }

            if ($type !== null) {
                $toInsert['type'] = $type;
                unset($request['type']);
            }

            $didUpdate = $vehicle_route->update($toInsert);
        }

        $didUpdate = $route->update($request);

        if (!$didUpdate) {
            return \Helper::instance()->horeca_http_not_updated();
        }

        return response('');
    }

    // Route::get('/routes/vehicle/{id}', 'VehicleRouteController@vehicle_route');
    public function vehicle_route($id)
    {
        $vehicle_route = VehicleRoute::where('vehicle_id', $id)->where('route_completed', false)->latest()->first();

        if (!$vehicle_route) {
            return \Helper::instance()->horeca_http_not_found(config('consts.no_route_for_vehicle'));
        }

        $route = Route::where('id', $vehicle_route->route_id)->first();

        if ($route) {
            return new RouteResource($route);
        }
        return \Helper::instance()->horeca_http_not_found(config('consts.not_found_route'));
    }

    public function conclude(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            array(
            'vehicle_id' => 'required',
            // 'bin_id' => 'required',
        )
        );

        if ($validator->fails()) {
            return response($validator->messages(), 400);
        }

        // VehicleRoute::where('vehicle_id', $request->input('vehicle_id'))->where('route_id', $id)->update(array('route_completed' => true, 'concluded_at_bin_id' => $request->input('bin_id')));
        VehicleRoute::where('vehicle_id', $request->input('vehicle_id'))->where('route_id', $id)->update(array('route_completed' => true, 'concluded_at_bin_id' => 0));

        // // Check what bins of the planned route where not picked up by the vehicle, and create a new route out of them
        // $bins = Route::where('id', $id)->pluck('bins')->first();

        // if ($request->input('bin_id') != end($bins)) {
        //     $bin_index = array_search($request->input('bin_id'), $bins);

        //     if ($bin_index) {
        //         $remaining = array_slice($bins, $bin_index + 1);
        //         $vehicle_plate = Vehicle::where('id', $request->input('vehicle_id'))->pluck('plates')->first();

        //         Route::insert(
        //             array(
        //                 'user_id' => 0,
        //                 'title' => 'Unfinished Route',
        //                 'description' => "This Route was left remaining from vehicle with plates $vehicle_plate.",
        //                 'bins' => json_encode($remaining),
        //                 'created_at' => now(),
        //                 'updated_at' => now(),
        //             )
        //         );

        //         return response('Created new Route from remaining bins!');
        //     }
        // }

        return response('Route completed successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\VehicleRoute  $vehicle_route
     * @return \Illuminate\Http\Response
     */
    public function destroy(VehicleRoute $vehicle_route)
    {
        //
    }
}
