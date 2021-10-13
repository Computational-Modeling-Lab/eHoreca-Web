<?php

namespace App\Http\Controllers;

use App\Models\Bin;
use App\Models\WProducer;
use Illuminate\Http\Request;

class BinController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toReturn = Bin::where('id', '>', 0);

        if ($request->query('type')) {
            $toReturn->where('type', $request->query('type'));
        }

        if ($request->input('public')) {
            $toReturn->where('isPublic', $request->query('public'));
        }

        $toReturn = $toReturn->get();

        return $toReturn;
    }

    public function reports($id)
    {
        $bin = Bin::find($id);

        if (count($bin->reports)) {
            $bin['reports'] = $bin->reports;
            return $bin;
        }

        return [];
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
                'lat' => 'required',
                'lng' => 'required',
                'capacity' => 'required',
                'capacity_unit' => 'required',
                'type' => 'required|in:compost,glass,recyclable,mixed,metal,paper,plastic',
                'description' => 'required',
                'quantity' => 'required',
            )
        );

        if ($validator->fails()) {
            return response($validator->messages(), 400);
        }

        if (!Bin::create(\Helper::instance()->horeca_request_with_point_from_latlng($request->toArray()))) {
            return \Helper::instance()->horeca_http_not_created();
        }

        return \Helper::instance()->horeca_http_created();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bin  $bin
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bin = Bin::find($id);

        if (!$bin) {
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_bin'));
        }

        return $bin;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bin  $bin
     * @return \Illuminate\Http\Response
     */
    public function edit(Bin $bin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bin  $bin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $bin = Bin::find($id);

        if (!$bin) {
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_bin'));
        }

        $didUpdate = $bin->update(\Helper::instance()->horeca_request_with_point_from_latlng($request->toArray()));

        if (!$didUpdate) {
            return \Helper::instance()->horeca_http_not_updated();
        }

        return response('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bin  $bin
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Bin::delete($id)) {
            return \Helper::instance()->horeca_http_not_deleted();
        }

        $w_producer_bin_arrays = WProducer::select('id', 'bins')->get();

        foreach ($w_producer_bin_arrays as $w_producers) {
            $key = array_search($id, $w_producers->bins);
            if ($key !==false) {
                $new_bins = $w_producers->bins;
                unset($new_bins[$key]);
                WProducer::where('id', $w_producers->id)->update(['bins' => json_encode($new_bins)]);
                // a bin cannot belong to more than one w_producer. If found, it's the only one, break the loop.
                break;
            }
        }

        return response('');
    }

    // Gets a request with a lat and lng and returns the bin object closest to those coords
    public function closest(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            array(
                'lat' => 'required',
                'lng' => 'required',
                'isDriver' => 'required',
            )
        );

        if ($validator->fails()) {
            return response($validator->messages(), 400);
        }

        $request = $request->toArray();
        $lat = $request['lat'];
        $lng = $request['lng'];

        $closest_bin = \DB::table('bins')->selectRaw("*, ST_AsText(location) AS location, ST_Length(LineStringFromWKB(LineString(location, POINT($lat, $lng)))) as distance");

        if (!$request['isDriver']) {
            $closest_bin->where('isPublic', true);
        }

        $closest_bin = $closest_bin->orderBy('distance')->first();
        $closest_bin = (array)$closest_bin;

        $closest_bin['location'] = \Helper::instance()->horeca_point_to_latlng($closest_bin['location']);
        return $closest_bin;
    }
}
