<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WProducer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toReturn = User::where('id', '>', 0);
        $total_results = count($toReturn->get());
        $total_pages = \Helper::instance()->get_total_pages(count($toReturn->get()));

        if ($request->query('role')) {
            $toReturn->where('role', '=', $request->query('role'));
        }

        if ($request->query('page')) {
            $toReturn->offset(config('consts.page_size') * ($request->query('page') - 1))->limit(config('consts.page_size'));
        }

        $toReturn = $toReturn->get();

        return array('results' => $toReturn, 'total_pages' => $total_pages, 'total_results' => $total_results);
    }

    public function heatmaps($id)
    {
        $user = User::find($id);

        if (count($user->heatmaps)) {
            return array('results' => $user->heatmaps, 'total_pages' => \Helper::instance()->get_total_pages(count($user->heatmaps)));
        }

        return array('results' => [], 'total_pages' => 0);
    }

    public function routes($id)
    {
        $user = User::find($id);

        if (count($user->routes)) {
            return array('results' => $user->routes, 'total_pages' => \Helper::instance()->get_total_pages(count($user->routes)));
        }
        return array('results' => [], 'total_pages' => 0);
    }

    public function reports($id)
    {
        $user = User::find($id);

        if (count($user->reports)) {
            return array('results' => $user->reports, 'total_pages' => \Helper::instance()->get_total_pages(count($user->reports)));
        }
        return array('results' => [], 'total_pages' => 0);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!$request->input('name') || !$request->input('surname') || !$request->input('email') || !$request->input('password')) {
            return \Helper::instance()->horeca_http_missing_data();
        }

        $request['password'] = Hash::make($request->input('password'));

        $user = User::where('email', '=', $request->input('email'))->withTrashed()->first();

        if ($user) return \Helper::instance()->horeca_http_not_created(config('consts.duplicate_email'));

        if ($request->input('role') === 'w_producer' || $request->input('role') === 'w_producer_employee') {
            if (!isset($request->w_producer_id)) return \Helper::instance()->horeca_http_not_created();
            $w_producer = WProducer::find($request->w_producer_id);
            if (!$w_producer) return \Helper::instance()->horeca_http_not_created();
        }

        $user = User::create($request->all());
        if (!$user) return \Helper::instance()->horeca_http_not_created();

        if ($w_producer) {
            $w_producer->users = app('App\Http\Controllers\WProducerController')->append_to_users_array($w_producer->users, $user->id);
            $w_producer->save();
        }

        return \Helper::instance()->horeca_http_created();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id) {
            $user = User::find($id);

            if (!$user) {
                return \Helper::instance()->horeca_http_not_found(config('consts.not_found_email'));
            }

            return $user;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_email'));
        }

        $didUpdate = $user->update($request->all());

        if (!$didUpdate) {
            return \Helper::instance()->horeca_http_not_updated();
        }

        return response('');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!User::destroy($id)) {
            return \Helper::instance()->horeca_http_not_deleted();
        }

        $w_producer_user_arrays = WProducer::select('id', 'users')->get();

        foreach ($w_producer_user_arrays as $w_producers) {
            $key = array_search($id, $w_producers->users);
            if ($key !== false) {
                $new_users = $w_producers->users;
                unset($new_users[$key]);
                WProducer::where('id', $w_producers->id)->update(['users' => json_encode($new_users)]);
                // a bin cannot belong to more than one w_producer. If found, it's the only one, break the loop.
                break;
            }
        }

        return response('');
    }

    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), array(
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ));

        if ($validator->fails()) {
            return response($validator->messages(), 400);
        }

        $userLoggedIn = Auth::attempt(['email' => $request->email, 'password' => $request->password], true);
        if (!$userLoggedIn) return response('Could not log in', 403);
       
        $user = Auth::user();
        if ($user) return response(['token' => $user->token, 'id' => $user->id], 200);
    }

    public function logout(Request $request)
    {
        $user = User::where('token', '=', $request->bearerToken())->first();
        Auth::logout();
        Session::flush();
        // Do not check if user exists. We know they do since they pass the middleware. If they do not, they will receive a response accordingly automatically
        $user->token = null;
        if (!$user->save()) {
            return \Helper::instance()->horeca_http_not_updated(config('consts.no_logout'));
        }

        return response('');
    }

    public function webLogout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return response('');
    }
}
