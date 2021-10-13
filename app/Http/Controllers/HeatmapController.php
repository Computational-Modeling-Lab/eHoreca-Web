<?php

namespace App\Http\Controllers;

use App\Http\Resources\HeatmapResource;
use App\Models\Heatmap;
use Illuminate\Http\Request;

class HeatmapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toReturn = Heatmap::where('id', '>', 0);

        if ($request->query('page'))
            $toReturn->offset(config('consts.page_size') * ($request->query('page') - 1))->limit(config('consts.page_size'));

        $toReturn = HeatmapResource::collection($toReturn->get());

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
        if (!$request->input('title') || !$request->input('description') || !$request->input('valid_from') || !$request->input('valid_to')) {
            return \Helper::instance()->horeca_http_missing_data();
        }

        if (!Heatmap::create($request->all())) {
            return \Helper::instance()->horeca_http_not_created();
        }

        return \Helper::instance()->horeca_http_created();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Heatmap  $Heatmap
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id) {
            $Heatmap = Heatmap::find($id);

            if (!$Heatmap) {
                return \Helper::instance()->horeca_http_not_found(config('consts.not_found_email'));
            }

            return new HeatmapResource($Heatmap);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Heatmap  $Heatmap
     * @return \Illuminate\Http\Response
     */
    public function edit(Heatmap $Heatmap)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Heatmap  $Heatmap
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $Heatmap = Heatmap::find($id);

        if (!$Heatmap) {
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_email'));
        }

        unset($request['created_by']);
        $didUpdate = $Heatmap->update($request->all());

        if (!$didUpdate) {
            return \Helper::instance()->horeca_http_not_updated();
        }

        return response('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Heatmap  $Heatmap
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Heatmap::delete($id))
            return \Helper::instance()->horeca_http_not_deleted();

        return response('');
    }
}
