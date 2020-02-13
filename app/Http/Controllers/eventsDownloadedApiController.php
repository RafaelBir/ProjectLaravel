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
     * all four columns are returned as a json object
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

    /**
     * Returns an array of size $n
     * index 0 represents for today while index $n represents $n days ago
     * each index includes the list of rows (download events) of the given day
     * all four columns are returned as a json object
     *
     * @param $n is an integer bigger than 0
     * @return JsonResponse
     */
    protected function nDays($n)
    {

        //Get the dates of the last 7 days in relation to today
        //Query the database and put the results on an array
        for ($x = 0; $x < $n; $x++) {
            $currentDay = date("Y-m-d", strtotime("-" . $x . " Days"));
            $results[$x] = DB::select("select * from episode_downloads where occurredAt like '$currentDay%' ", array(1));
        }


        //Create a json response and send it
        return new JsonResponse($results);

    }

    /**
     * Returns the list of rows (download events) in the given $date
     *
     * @param $date is a date in "yyyy-mm-dd"
     * @return JsonResponse
     */
    protected function thatDay($date)
    {

        //Get the events on that specific day
        $result = DB::select("select * from episode_downloads where occurredAt like '$date%' ", array(1));

        //Create a json response and send it
        return new JsonResponse($result);

    }

}
