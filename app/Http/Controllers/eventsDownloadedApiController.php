<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EpisodeDownloadedApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | EpisodeDownloaded Api Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the response to the request of last 7 day's
    | episode download list using a json response.
    |
    */

    /**
     * Returns an array of size 6
     * index 0 represents for today while index 6 represents 6 days ago
     * each index includes the list of rows (download events) of the given day
     * all four columns are returned
     *
     * @return JsonResponse
     */
    protected function sevenDays()
    {

        //Get the dates of the last 7 days in relation to today
        //Query the database and put the results on an array
        for ($x = 0; $x < 7; $x++) {
            $currentDay = date("Y-m-d", strtotime("-" . $x . " Days"));
            $results[$x] = DB::select("select * from episode_downloads where occurredAt like '$currentDay%' ", array(1));
        }


        //Create a json response and send it
        return new JsonResponse($results);

    }

}
