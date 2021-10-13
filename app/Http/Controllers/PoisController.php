<?php

namespace App\Http\Controllers;

use App\Models\Pois;
use Illuminate\Http\Request;

class POISController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toReturn = Pois::where('id', '>', 0);

        if ($request->query('page'))
            $toReturn->offset(config('consts.page_size') * ($request->query('page') - 1))->limit(config('consts.page_size'))->get();

        $toReturn = $toReturn->get();

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
        $validator = \Validator::make(
            $request->all(),
            array(
                'title' => 'required',
                'description' => 'required',
                'location' => 'required',
            )
        );

        if ($validator->fails())
            return response($validator->messages(), 400);

        if (!Pois::create(\Helper::instance()->horeca_request_with_point_from_latlng($request->toArray())))
            return \Helper::instance()->horeca_http_not_created();

        return \Helper::instance()->horeca_http_created();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pois  $Pois
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $poi = Pois::find($id);

        if (!$poi)
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_bin'));

        return $poi;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pois  $Pois
     * @return \Illuminate\Http\Response
     */
    public function edit(Pois $Pois)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pois  $Pois
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $poi = Pois::find($id);

        if (!$poi)
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_bin'));

        $didUpdate = $poi->update($request->all());

        if (!$didUpdate)
            return \Helper::instance()->horeca_http_not_updated();

        return response('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pois  $Pois
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Pois::delete($id))
            return \Helper::instance()->horeca_http_not_deleted();

        return response('');
    }
}
