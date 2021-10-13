<?php

namespace App\Helpers;

class HelperFunctions
{
    public static function instance()
    {
        return new HelperFunctions();
    }

    public function horeca_get_http_responses($override_message = null)
    {
        return array(
            'errors' => array(
                'bad_data' => array(
                    'code' => 400,
                    'message' => $override_message ? $override_message : 'Invalid data'
                ),
                'not_updated' => array(
                    'code' => 400,
                    'message' => $override_message ? $override_message : 'Could not update data'
                ),
                'not_deleted' => array(
                    'code' => 400,
                    'message' => $override_message ? $override_message : 'Could not delete data'
                ),
                'no_access' => array(
                    'code' => 401,
                    'message' => $override_message ? $override_message : 'You are not authorized to make this call'
                ),
                'not_created' => array(
                    'code' => 403,
                    'message' => $override_message ? $override_message : 'Could not create'
                ),
                'not_found' => array(
                    'code' => 404,
                    'message' => $override_message ? $override_message : 'This item does not exist in our database'
                ),
                'missing_data' => array(
                    'code' => 422,
                    'message' => $override_message ? $override_message : 'Missing data'
                ),
            ),
            'success' => array(
                'created' => array(
                    'code' => 201,
                    'message' => $override_message ? $override_message : 'Successfully created',
                )
            )
        );
    }

    /**
     * Checks if email is already registered in the DB
     *
     * @param String $email
     * @return int count. Used as "truthy"/"falsey"
     */
    public function horeca_is_duplicate_email($email)
    {
        // if no users are found it's "falsey" therefore not a duplicate email
        return count(\DB::table('users')->where('email', '=', $email)->get());
    }

    /**
     * Retrieves the user's id according to their token
     *
     * @param String $api_token
     * @return String $user_id
     */
    public function horeca_get_user_id_by_token($api_token)
    {
        $user = \DB::table('users')->where('apiToken', '=', $api_token)->first();

        if ($user) {
            return (string)$user['_id'];
        }
    }

    /**
     * Stores a base64 string as a file in the public directory
     *
     * @param String $path
     * @param String $filename
     * @param String $base64
     * @return Boolean
     *
     */
    public function horeca_store_base64(String $path, String $filename, String $base64)
    {
        return \Storage::disk('public')->put("$path/$filename", base64_decode($base64));
    }

    /**
     * Returns the full path of the public directory
     *
     * @return String
     */
    public function horeca_get_public_path()
    {
        return \Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix();
    }

    /** FIXED HTTP RESPONSES */

    // Successfull POST response (201)
    public function horeca_http_created($override_message = null)
    {
        $response = $this->horeca_get_http_responses($override_message);
        return response($response['success']['created'], $response['success']['created']['code']);
    }

    // Bad data response
    public function horeca_http_bad_data($override_message = null)
    {
        $response = \Helper::instance()->horeca_get_http_responses($override_message);
        return response($response['errors']['bad_data'], $response['errors']['bad_data']['code']);
    }

    // Mising data response
    public function horeca_http_missing_data($override_message = null)
    {
        $response = $this->horeca_get_http_responses($override_message);
        return response($response['errors']['missing_data'], $response['errors']['missing_data']['code']);
    }

    // Wrong password response
    public function horeca_http_no_access($override_message = null)
    {
        $response = $this->horeca_get_http_responses($override_message);
        return response($response['errors']['no_access'], $response['errors']['no_access']['code']);
    }

    // Not created response
    public function horeca_http_not_created($override_message = null)
    {
        $response = $this->horeca_get_http_responses($override_message);
        return response($response['errors']['not_created'], $response['errors']['not_created']['code']);
    }

    // Not updated
    public function horeca_http_not_updated($override_message = null)
    {
        $response = $this->horeca_get_http_responses($override_message);
        return response($response['errors']['not_updated'], $response['errors']['not_updated']['code']);
    }

    // Not deleted
    public function horeca_http_not_deleted($override_message = null)
    {
        $response = $this->horeca_get_http_responses($override_message);
        return response($response['errors']['not_deleted'], $response['errors']['not_deleted']['code']);
    }

    // Not found response (404)
    public function horeca_http_not_found($override_message = null)
    {
        $response = \Helper::instance()->horeca_get_http_responses($override_message);
        return response($response['errors']['not_found'], $response['errors']['not_found']['code']);
    }

    // changes a request point with latitude and longitude to work in the database
    public function horeca_request_with_point_from_latlng($request)
    {
        $lat = $request['lat'];
        $lng = $request['lng'];

        $request['location'] = \DB::raw("ST_GeomFromText('POINT($lat $lng)')");

        unset($request['lat']);
        unset($request['lng']);

        return $request;
    }

    // changes the location point from the database to latitude and longitude
    public function horeca_point_to_latlng($location)
    {
        $location = substr(explode('POINT(', $location)[1], 0, -1);
        $location = explode(' ', $location);
        $location = array(
            'lat' => $location[0],
            'lng' => $location[1],
        );

        return $location;
    }

    public function get_total_pages($numberOfResults)
    {
        $total = intval($numberOfResults / config('consts.page_size'));

        if ($total < $numberOfResults)
            $total = $total + 1;

        return $total;
    }
}
