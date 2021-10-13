<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReportResource;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $toReturn = Report::where('w_producer_id', null)->orderBy('id', 'DESC');

        if ($request->query('user_id'))
            $toReturn->where('user_id', $request->query('user_id'));

        if ($request->input('approved'))
            $toReturn->where('approved', true);

        if ($request->query('page'))
            $toReturn->offset(config('consts.page_size') * ($request->query('page') - 1))->limit(config('consts.page_size'));

        $toReturn = ReportResource::collection($toReturn->get());

        return array('results' => $toReturn, 'total_pages' => \Helper::instance()->get_total_pages(count($toReturn)));
    }

    // Route::get('/reports/w_producer/{id}', 'ReportController@w_producer_reports');
    public function w_producer_reports($id)
    {
        $toReturn = Report::where('w_producer_id', $id)->orderBy('id', 'DESC');

        $toReturn = ReportResource::collection($toReturn->get());

        return $toReturn;
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
                'bin_id' => 'required',
                'user_id' => 'required',
                'location_accuracy' => 'required',
                'issue' => 'required|in:bin full,bin almost full,bin damaged,bin missing',
            )
        );

        if ($validator->fails())
            return response($validator->messages(), 400);


        $data = $request->toArray();
        unset($data['images']);

        $id = Report::create(\Helper::instance()->horeca_request_with_point_from_latlng($data))->id;
        if (!$id)
            return \Helper::instance()->horeca_http_not_created();

        if ($request->images) {
            $paths = [];
            foreach ($request->images as $image) {
                $filepath = config('consts.report_photos_dir') . "/bin__$id/" . \Str::random(4) . '.jpg';
                \Storage::disk('public')->put($filepath, base64_decode($image));
                $paths[] = $filepath;
            }

            $report = Report::find($id);
            $report->report_photos_filenames = !$report->report_photos_filenames ? $paths : array_merge($report->report_photos_filenames, $paths);
            $report->save();
        }

        return \Helper::instance()->horeca_http_created();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if ($id) {
            $report = Report::find($id);

            if (!$report)
                return \Helper::instance()->horeca_http_not_found(config('consts.not_found_report'));

            return new ReportResource($report);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report, $id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $report = Report::find($id);

        if (!$report)
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_report'));

        $data = $request->toArray();
        unset($data['created_by']);

        $data['approved'] = $data['approved'] === 'Yes' ? 1 : 0;
        $data['location_accuracy'] = trim(str_replace('m.', '', $data['location_accuracy']));

        $didUpdate = $report->update(\Helper::instance()->horeca_request_with_point_from_latlng($data));

        if (!$didUpdate)
            return \Helper::instance()->horeca_http_not_updated();

        return response('');
    }

    public function approve_report($id)
    {
        if (!Report::where('id', $id)->update(array('approved' => true)))
            return \Helper::instance()->horeca_http_not_updated();

        return response('');
    }

    public function delete_photo(Request $request, $id)
    {
        $report = Report::find($id);

        if (!$report)
            return \Helper::instance()->horeca_http_not_found(config('consts.not_found_report'));

        $photos = $report['report_photos_filenames'];

        if (isset($photos[$request->input('index')])) {
            \Storage::delete(explode('app/', $photos[$request->input('index')])[1]);
            unset($photos[$request->input('index')]);
        }

        $report->report_photos_filenames = $photos;
        if ($report->save()) return response('');

        return \Helper::instance()->horeca_http_not_deleted();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!Report::delete($id))
            return \Helper::instance()->horeca_http_not_deleted();

        return response('');
    }
}
