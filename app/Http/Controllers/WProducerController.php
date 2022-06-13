<?php

namespace App\Http\Controllers;

use App\Models\WProducer;
use App\Models\Bin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WProducerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toReturn = WProducer::where('id', '>', 0);
        $total_results = count($toReturn->get());
        $total_pages = \Helper::instance()->get_total_pages(count($toReturn->get()));

        if ($request->query('page')) {
            $toReturn->offset(config('consts.page_size') * ($request->query('page') - 1))->limit(config('consts.page_size'));
        }

        $toReturn = $toReturn->get();

        return array('results' => $toReturn, 'total_pages' => $total_pages, 'total_results' => $total_results);
    }

    // Route::get('/w_producers/from_user_id/{id}', 'WProducerController@from_user_id');
    // Append the userId as a url parameter, find the w_producer with this userId inside the users array and return the w_producer
    public function from_user_id($id)
    {
        $toReturn = WProducer::get();
        foreach($toReturn as $w_producerRecord) {
            if (isset($w_producerRecord->users) && in_array($id, $w_producerRecord->users)) {
                return $w_producerRecord;
            }
        };
        // $toReturn = WProducer::where('users', "$id")->get();

        // if (isset($toReturn) && count($toReturn) > 0) {
        //     return $toReturn;
        // }

        return \Helper::instance()->horeca_http_not_found(config('consts.not_found_w_producer'));
    }

    public function approve_wprod($id)
    {
        $wProducer = WProducer::where('id', $id)->first();
        if (!$wProducer) {
            return \Helper::instance()->horeca_http_not_updated();
        }
        $wProducer->update(array('is_approved' => true));
        return response('Producer approved');
    }

    // Route::post('/w_producers/employer', 'WProducerController@new_employer');
    // Create a new user, create a new w_producer, and add the user ID to the users column
    public function new_employer(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            array(
                'name' => 'required',
                'surname' => 'required',
                'email' => 'required',
                'username' => 'required',
                'password' => 'required',
                'contact_telephone' => 'required',
            )
        );

        if ($validator->fails()) {
            return response($validator->messages(), 400);
        }

        $userResult = $this->user_exists($request->input('email')); // get the user status
        $w_producer = null;

        $contact_name = "";
        if (!isset($request['contact_name'])) {
            $contact_name = $request['name'] . " " . $request['surname'];
        } else {
            $contact_name = $request['contact_name'];
        }

        // most likely, producer
        if (isset($request['join_pin'])) {
            $w_producer = $this->producer_exists($request->input('join_pin'));
            if ($w_producer) return response('Join pin already exists', 406);
        }

        $request = $request->all();

        $toInsertUser = array(
            'name' => $request['name'],
            'surname' => $request['surname'],
            'email' => $request['email'],
            'username' => $request['username'],
            'password' => $request['password'],
            'role' => 'w_producer',
        );
        $toInsertWproducer = array(
            'title' => $request['title'],
            'contact_name' => $contact_name,
            'join_pin' => $request['join_pin'],
            'contact_telephone' => $request['contact_telephone'],
            'lat' => $request['lat'],
            'lng' => $request['lng'],
            'description' => $request['description'] ?? '',
        );

        $toInsertWproducer = \Helper::instance()->horeca_request_with_point_from_latlng($toInsertWproducer);

        if (isset($request['description'])) {
            $toInsertWproducer['description'] = $request['description'];
        }

        if (isset($userResult[1])) {
            return $userResult[0];
        }

        if (!isset($toInsertWproducer['contact_name'])) {
            $toInsertWproducer['contact_name'] = $request['name'] . " " . $request['surname'];
        }

        $toInsertWproducer['bins'] = json_encode(array());

        if ($userResult) {   // the user exists, do not create a new one
            if (!$this->is_user_auth($request['email'], $request['password'])) {
                return \Helper::instance()->horeca_http_no_access(config('consts.wrong_pass'));
            }

            if ($w_producer) {
                $w_producer = WProducer::find($w_producer);
                $w_producer->title = $toInsertWproducer['title'];
                $w_producer->contact_name = $toInsertWproducer['comtact_name'] ?? $request['name'] . " " . $request['surname'];
                $w_producer->join_pin = $toInsertWproducer['join_pin'];
                $w_producer->contact_telephone = $toInsertWproducer['contact_telephone'];
                $w_producer->location = $toInsertWproducer['location'];
                $w_producer->description = $toInsertWproducer['desciption'] ?? '';
                $w_producer->users = $this->append_to_users_array($w_producer->users, $userResult);
                $w_producer->save();
            } else {
                $this->insertWProducer(json_encode(array($userResult)), $toInsertWproducer, false);
            }

            $user = User::find($userResult);
            $user->role = 'w_producer';
            if ($user->save()) {
                return \Helper::instance()->horeca_http_created();
            }
        } else { // the user does not exist
            $toInsertUser['password'] = Hash::make($toInsertUser['password']);

            $userID = User::create($toInsertUser)->id;
            if (!$userID) {
                return \Helper::instance()->horeca_http_not_created();
            }

            if ($w_producer !== false) {
                $w_producer = WProducer::find($w_producer);
                $w_producer->title = $toInsertWproducer['title'];
                $w_producer->contact_name = $toInsertWproducer['comtact_name'] ?? $request['name'] . " " . $request['surname'];
                $w_producer->join_pin = $toInsertWproducer['join_pin'];
                $w_producer->contact_telephone = $toInsertWproducer['contact_telephone'];
                $w_producer->location = $toInsertWproducer['location'];
                $w_producer->description = $toInsertWproducer['desciption'] ?? '';
                $w_producer->users = $this->append_to_users_array($w_producer->users, $userID);
                if ($w_producer->save()) {
                    return \Helper::instance()->horeca_http_created();
                }
            }

            $this->insertWProducer(json_encode(array($userID)), $toInsertWproducer, $userID);
        }

        return \Helper::instance()->horeca_http_created();
    }

    private function insertWProducer($users, $request, $userID = false)
    {
        $request['users'] = $users;
        $request['bins'] = json_encode(array());
        // $request = \Helper::instance()->horeca_request_with_point_from_latlng($request);
        if ($userID !== false) {
            if (!WProducer::insert($request)) {
                User::delete($userID);

                return \Helper::instance()->horeca_http_not_created();
            }
        } else {
            if (!WProducer::insert($request)) {
                return \Helper::instance()->horeca_http_not_created();
            }
        }
    }

    // Route::post('/w_producers/employee/{id}', 'WProducerController@new_employee');
    // Check if the user provided a join_pin, create a new user, and add the user ID to the users column
    public function new_employee(Request $request)
    {
        if ($request->join_pin) {
            $w_producer = WProducer::where('join_pin', $request->input('join_pin'))
            ->where('id', $request->id)
            ->first();
            if ($w_producer) {
                $userResult = $this->user_exists($request->input('email')); // get the user status
                $user_id = 0;

                if ($userResult) return response('User with this email already exists', 406);
                else {
                    unset($request['join_pin']);
                    $request['role'] = 'w_producer_employee';
                    $request['password'] = Hash::make($request['password']);
                    $user_id = User::create($request->all())->id;
                }

                if ($user_id) {
                    $w_producer->users = $this->append_to_users_array($w_producer->users, $user_id);
                    $w_producer->save();
                    return response('User created', 200);
                }
            }

            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_w_producer'));
        }

        return response('Join pin required', 400);
    }

    // Check if the user already exists, if yes return the id, else return false
    private function user_exists(String $email)
    {
        $user = User::where('email', $email)->get()->first();
        if ($user) {
            return $user->id;
        }

        return false;
    }

    // Check if the producer already exists, if yes return the id, else return false
    private function producer_exists(String $join_pin)
    {
        $producer = WProducer::where('join_pin', $join_pin)->get()->first();
        if ($producer) {
            return $producer->id;
        }

        return false;
    }

    private function is_user_auth(String $email, String $password)
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            return Hash::check($password, $user['password']);
        }
    }

    public function append_to_users_array($w_producer_users, $user)
    {
        if (!isset($w_producer_users)) {
            return [$user];
        }
        
        if (isset($w_producer_users) && !in_array($user, $w_producer_users)) {
            return array_merge($w_producer_users, [$user]);
        }

        return $w_producer_users;
    }

    // Route::post('/w_producers/new_bin', 'WProducerController@new_bin');
    // Create a new bin, and add the bin ID to the bins column
    public function new_bin(Request $request)
    {
        // return gettype($request->bin);
        $validator = \Validator::make(
            $request->all(),
            [
                'w_producer_id' => 'required',
                'bin.lat' => 'required',
                'bin.lng' => 'required',
                'bin.capacity' => 'required',
                'bin.capacity_unit' => 'required',
                'bin.type' => 'required|in:compost,glass,recyclable,mixed,metal,paper,plastic',
                'bin.description' => 'required',
                'bin.quantity' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response($validator->messages(), 400);
        }

        $insertBin = $request->bin;
        $insertBin['isPublic'] = false;

        $bin_id = Bin::create(\Helper::instance()->horeca_request_with_point_from_latlng($insertBin))->id;

        if ($bin_id) {
            $w_producer = WProducer::where('id', $request->w_producer_id)->first();
            $w_producer->bins = isset($w_producer->bins) && count($w_producer->bins) ? array_merge($w_producer->bins, [$bin_id]) : [$bin_id];
            $w_producer->save();
            return \Helper::instance()->horeca_http_created();
        }

        return \Helper::instance()->horeca_http_not_created();
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
                'title' => 'required',
                'contact_name' => 'required',
                'contact_telephone' => 'required',
            )
        );

        if ($validator->fails()) {
            return response($validator->messages(), 400);
        }

        $request['users'] = json_encode($request['users']);
        $request['bins'] = json_encode($request['bins']);

        if (!WProducer::create(\Helper::instance()->horeca_request_with_point_from_latlng($request->toArray()))) {
            return \Helper::instance()->horeca_http_not_created();
        }

        return \Helper::instance()->horeca_http_created();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\WProducer  $WProducer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $WProducer = WProducer::find($id);

        if (!$WProducer) {
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_w_producer'));
        }

        return $WProducer;
    }

    public function bins($id)
    {
        $toReturn = array();

        $w_producer_bin_ids = WProducer::where('id', $id)->pluck('bins')->first();

        foreach ($w_producer_bin_ids as $bin_id) {
            $bin = Bin::where('id', $bin_id)->first();
            if ($bin) array_push($toReturn, $bin);
        }

        return $toReturn;
    }

    public function users($id)
    {
        $toReturn = array();

        $w_producer_users_ids = WProducer::where('id', $id)->pluck('users')->first();

        foreach ($w_producer_users_ids as $user) {
            array_push($toReturn, User::where('id', $user)->get()->first());
        }

        return $toReturn;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\WProducer  $WProducer
     * @return \Illuminate\Http\Response
     */
    public function edit(WProducer $WProducer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\WProducer  $WProducer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $WProducer = WProducer::find($id);

        if (!$WProducer) {
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_w_producer'));
        }

        $didUpdate = $WProducer->update($request->all());

        if (!$didUpdate) {
            return \Helper::instance()->horeca_http_not_updated();
        }

        return response('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\WProducer  $WProducer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $wproducer = WProducer::find($id);
        if (!$wproducer) {
            return \Helper::instance()->horeca_http_not_deleted();
        }
        $wproducer->delete();
        return response('W_producer deleted');
    }

    // w_producer id
    public function destroy_bin(Request $request, $id)
    {
        $w_producer = WProducer::find($id);

        if (!$w_producer) {
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_w_producer'));
        }
        
        if (in_array($request->input('bin_id'), $w_producer->bins)) {
            $new_bins = $w_producer->bins;
            $key = array_search($request->input('bin_id'), $new_bins);
            if ($key !== false) {
                unset($new_bins[$key]);
                $w_producer->bins = array_merge($new_bins, []);
                $w_producer->save();
                $bin = Bin::where('id', $request->input('bin_id'))->first()->delete();
                return response('Bin Deleted');
            }
        }
        return \Helper::instance()->horeca_http_not_found(config('consts.not_found_bin'));
    }
}
