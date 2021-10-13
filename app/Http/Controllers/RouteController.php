<?php

namespace App\Http\Controllers;

use App\Http\Resources\RouteResource;
use App\Models\Route;
use App\Models\VehicleRoute;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toReturn = Route::where('id', '>', 0)->orderBy('id', 'DESC');

        if ($request->query('page')) {
            $toReturn->offset(config('consts.page_size') * ($request->query('page') - 1))->limit(config('consts.page_size'));
        }

        $toReturn = RouteResource::collection($toReturn->get());

        return array('results' => $toReturn, 'total_pages' => \Helper::instance()->get_total_pages(count($toReturn)));
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), array(
            'title' => 'required',
            'description' => 'required',
            'bins' => 'required',
        ));

        if ($validator->fails()) {
            return response($validator->messages(), 400);
        }

        $request['bins'] = is_array($request['bins']) ? json_encode($request['bins']) : $request['bins'];
        $request['outcome'] = is_array($request['outcome']) ? json_encode($request['outcome']) : $request['outcome'];

        if (!Route::create($request->all())) {
            return \Helper::instance()->horeca_http_not_created();
        }

        return \Helper::instance()->horeca_http_created();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $route = Route::find($id);
        if (!$route) {
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_email'));
        }

        return new RouteResource($route);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Route  $Route
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $route = Route::find($id);

        if (!$route) {
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_email'));
        }

        unset($request['created_by']);

        if (isset($request['outcome'])) {
            $request['outcome'] = is_array($request['outcome']) ? json_encode($request['outcome']) : $request['outcome'];
        }

        $didUpdate = $route->update($request->all());

        if (!$didUpdate) {
            return \Helper::instance()->horeca_http_not_updated();
        }

        return response('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Route  $route
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        VehicleRoute::where('route_id', $id)->delete();

        if (!Route::delete($id)) {
            return \Helper::instance()->horeca_http_not_deleted();
        }

        return response('');
    }
}
