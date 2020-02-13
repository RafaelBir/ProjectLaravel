<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EventsDownloadedApiControllerTest extends TestCase
{
    /**
     * Test when the database is empty if everything goes well
     *
     * @return void
     * @test
     */
    public function testEmptyResponse()
    {
        $response = $this->json('get', '/api/episodeRecord');

        $response->assertExactJson(
            [[],[],[],[],[],[],[]]
        );
    }

    /**
     * Test when the database is not empty but every download is older than seven days
     *
     * @return void
     * @test
     */
    public function testEmptyResponseWithData()
    {
        //Get the dates of the last 7 days in relation to today
        //Query the database and put the results on an array
        for ($x = 0; $x < 7; $x++) {
            $currentDay = date("Y-m-d", strtotime("-" . $x . " Days"));
            $results[$x] = DB::select("select * from episodedownloaded where occurredAt like '$currentDay%' ", array(1));
        }


        $response = $this->json('get', '/api/episodeRecord');

        $response->assertExactJson(
            [[],[],[],[],[],[],[]]
        );
    }
}
