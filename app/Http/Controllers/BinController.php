<?php

namespace App\Http\Controllers;

use App\Models\Bin;
use App\Models\User;
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
        $user = User::find($request->user_id);
        if ($user) {
            if ($user->role==='public') return Bin::where('ispublic', true)->get();
            if ($user->role==='admin') {
                return Bin::where(
                    function ($query) use ($request) {
                        if ($request->type) $query
                        ->where('type', $request->type);
                    }
                )
                ->get();
            } 
            if ($user->role!=='admin' && $user->role!=='public') {
                $wProducers = WProducer::all();
                $producer = null;
                foreach($wProducers as $w_producerRecord) {
                    if (isset($w_producerRecord->users) && in_array($request->user_id, $w_producerRecord->users)) {
                        $producer = $w_producerRecord;
                    }
                };
                if (!$producer || (isset($producer) && !$producer->bins)) return [];
                return Bin::whereIn('id', $producer->bins)
                ->where(
                    function ($query) use ($request) {
                        if ($request->type) $query
                        ->where('type', $request->type);
                    }
                )
                ->get();
            } 
        }
        $toReturn = Bin::where('id', '>', 0);

        if ($request->query('type')) {
            $toReturn->where('type', $request->query('type'));
        }

        if ($request->input('public')) {
            $toReturn->where('isPublic', $request->query('public'));
        }
        return $toReturn->get();
    }

    public function reports($id)
    {
        $bin = Bin::findorfail($id);

        if ($bin->reports) return $bin->reports;
        
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

        if (isset($request->isPublic)) $request->isPublic = filter_var($request->isPublic, FILTER_VALIDATE_BOOLEAN);
        if (isset($request->isPublic) && !$request->isPublic && !$request->w_producer_id) return \Helper::instance()->horeca_http_not_created();


        $bin = Bin::create(\Helper::instance()->horeca_request_with_point_from_latlng($request->toArray()));
        if (!$bin) return \Helper::instance()->horeca_http_not_created();
        
        if (isset($request->w_producer_id) && (isset($request->isPublic) && $request->isPublic == false) && $bin) {
            $w_producer = WProducer::where('id', $request->w_producer_id)->first();
            $w_producer->bins = isset($w_producer->bins) && count($w_producer->bins) ? array_merge($w_producer->bins, [$bin->id]) : [$bin->id];
            $w_producer->save();
            return \Helper::instance()->horeca_http_created();
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

        // Transform request field to array and loop over them fields to update only sent fields
        $fields = $request->toArray();
        foreach ($fields as $key=>$value) {
            if ($value && $value!=='' && $key!=='lat' && $key!=='lng') $bin->$key = $value;
        }

        $bin->save();

        // Used to transform lng and lat fields to point.
        if ($request->lat && $request->lng) {
            $didUpdate = $bin->update(\Helper::instance()->horeca_request_with_point_from_latlng($request->toArray()));

            if (!$didUpdate) {
                return \Helper::instance()->horeca_http_not_updated();
            }
        }
        return response('Success', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bin  $bin
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $bin = Bin::find($id);
        if (!$bin) {
            return \Helper::instance()->horeca_http_not_deleted();
        }
        $bin->delete();
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


        function distance($lat1, $lon1, $lat2, $lon2, $unit) {
            if (($lat1 == $lat2) && ($lon1 == $lon2)) {
                return 0;
            }
            else {
                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $unit = strtoupper($unit);
            
                if ($unit == "K") {
                return ($miles * 1.609344);
                } else if ($unit == "N") {
                return ($miles * 0.8684);
                } else {
                return $miles;
                }
            }
        }

        $bins = Bin::get();
        // if (!$request['isDriver']) {
        //     $bins = $bins->where('isPublic', true);
        // }
        $closestDistance = null;
        foreach($bins as $bin) {
            $bin->location['lat'];
            $closestDistanceTemp = distance($lat, $lng, $bin->location['lat'], $bin->location['lng'], 'K');
            if ($closestDistance === null) {
                $closestDistance = $closestDistanceTemp;
                $closest_bin = $bin;
                continue;
            } 
            if ($closestDistanceTemp < $closestDistance) {
                $closestDistance = $closestDistanceTemp;
                $closest_bin = $bin;
            }
        }
        // $closest_bin = \DB::table('bins')->selectRaw("*, ST_AsText(location) AS location, ST_Length(LineStringFromWKB(LineString(location, POINT($lat, $lng)))) as distance");

        // if (!$request['isDriver']) {
        //     $closest_bin->where('isPublic', true);
        // }

        // $closest_bin = $closest_bin->orderBy('distance')->first();
        // $closest_bin = (array)$closest_bin;

        // $closest_bin['location'] = \Helper::instance()->horeca_point_to_latlng($closest_bin['location']);
        return $closest_bin;
    }
}
