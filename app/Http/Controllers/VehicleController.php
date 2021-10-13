<?php

namespace App\Http\Controllers;

use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use App\Models\VehicleRoute;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toReturn = Vehicle::where(function ($query) use ($request) {
            if ($request->query('type')) {
                $query->where('type', $request->query('type'));
            }

            if ($request->query('municipality')) {
                $query->where('municipality', $request->query('municipality'));
            }

            if ($request->query('in_service')) {
                $query->where('in_service', $request->query('in_service'));
            }

            if ($request->query('unity')) {
                $query->where('unity', $request->query('unity'));
            }

            if ($request->query('min_payload') && $request->query('payload_unit')) {
                $query->where('payload', '>=', $request->query('min_payload'))->where('capacity_unity', "=", $request->query('min_payload'));
            }

            if ($request->query('max_payload') && $request->query('payload_unit')) {
                $query->where('payload', '<=', $request->query('max_payload'))->where('capacity_unity', "=", $request->query('min_payload'));
            }

            if ($request->query('after')) {
                $query->where('year_first_license', '>', $request->query('after'));
            }

            if ($request->query('before')) {
                $query->where('year_first_license', '<', $request->query('before'));
            }

            if ($request->query('order')) {
                $query->orderBy($request->query('order'), 'ASC');
            }
        });

        if ($request->query('page')) {
            $toReturn->offset(config('consts.page_size') * ($request->query('page') - 1))->limit(config('consts.page_size'));
        }

        $toReturn = VehicleResource::collection($toReturn->get());

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
                'plates' => 'required',
                'type' => 'required|in:Φ.Ι.Χ. Ανοικτό,Φ.Ι.Χ. Κλειστό,Απορριμματοφόρο,Πλυντήριο κάδων,Σάρωθρο,Φ.Ι.Χ Τρίκυκλο,Φ.Ι.Χ. Ανατρεπόμενο,Φ.Ι.Χ. Βυτιοφόρο,Φ.Ι.Χ. Τράκτορας',
                'make' => 'required',
                'year_first_license' => 'required',
                'taxable_hp' => 'required',
                'payload' => 'required',
                'payload_unit' => 'required',
                'municipality' => 'required|in:ΚΕΡΚΥΡΑΙΩΝ,ΜΕΛΙΤΕΙΕΩΝ,ΘΙΝΑΛΙΩΝ,ΦΑΙΑΚΩΝ,ΕΣΠΕΡΙΩΝ,ΠΑΡΕΛΙΩΝ,ΑΧΙΛΛΕΙΩΝ,ΚΑΣΣΩΠΑΙΩΝ,ΠΑΛΑΙΟΚΑΣΤΡΙΤΩΝ,ΛΕΥΚΙΜΜΑΙΩΝ,ΑΓ. ΓΕΩΡΓΙΟΥ,ΚΟΡΙΣΣΙΩΝ',
                'unity' => 'required|in:Διοίκησης,Ηλεκτροφωτισμού,Καθαριότητας',
                'in_service' => 'required',
            )
        );

        if ($validator->fails()) {
            return response($validator->messages(), 400);
        }

        $toInsert = $request->toArray();
        $toInsert['plates'] = strtoupper($toInsert['plates']);

        if (!Vehicle::create($toInsert)) {
            return \Helper::instance()->horeca_http_not_created();
        }

        return \Helper::instance()->horeca_http_created();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id) {
            $vehicle = Vehicle::find($id);

            if (!$vehicle) {
                return \Helper::instance()->horeca_http_not_found(config('consts.not_found_vehicle'));
            }

            return new VehicleResource($vehicle);
        }
    }

    // Route::get('/vehicles/plate/{plate}', 'VehicleController@from_plate');
    public function from_plate($plate)
    {
        if ($plate) {
            $vehicle = Vehicle::where('plates', $plate)->first();

            if (!$vehicle) {
                return \Helper::instance()->horeca_http_not_found(config('consts.not_found_vehicle'));
            }

            return new VehicleResource($vehicle);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function edit(Vehicle $vehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::find($id);

        if (!$vehicle) {
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_vehicle'));
        }

        $request['year_first_license'] = $request['first_year_of_license'];
        $request['taxable_hp'] = $request['horsepower'];
        $request['in_service'] = $request['in_service'] === "Yes" ? 1 : 0;

        unset($request['first_year_of_license']);
        unset($request['horsepower']);
        $didUpdate = $vehicle->update($request->all());

        if (!$didUpdate) {
            return \Helper::instance()->horeca_http_not_updated();
        }

        return response('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vehicle  $vehicle
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        VehicleRoute::where('vehicle_id', $id)->delete();

        if (!Vehicle::delete($id)) {
            return \Helper::instance()->horeca_http_not_deleted();
        }

        return response('');
    }
}
