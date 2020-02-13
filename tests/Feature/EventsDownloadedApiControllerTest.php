<?php

namespace Tests\Feature;

use App\EpisodeDownload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
//use App\EpisodeDownload;
//use database\factories\EpisodeDownloadFactory;

class EventsDownloadedApiControllerTest extends TestCase
{
    /**
     *
     */
    public function setUp(): void
    {
        parent::setUp();
        Artisan::call('migrate:fresh --path=/database/migrations/2020_02_06_000000_create_episodeownloaded_table.php');
        //Artisan::call('db:seed', ['--class' => 'TestDatabaseSeeder', '--database' => 'testing']);
    }

    /**
     * Test when the database is empty if everything goes well
     *
     * @return void
     * @test
     */
    public function testEmptyResponse()
    {
        $response = $this->json('get', '/api/episodeRecord');

        $response
            ->assertStatus(200)
            ->assertExactJson(
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
        //first day to be not included
        $sevenDysAgo = date("Y-m-d", strtotime("-7 Days"));

        //all the values apart from the date are arbitrary and can be anything
        //the reason I do not let the faker assign random values to them is to be able to use assertExactJson
        factory(EpisodeDownload::class)->create([
            'occurredAt' => $sevenDysAgo.' 08:19:58',
            'eventId' => 'something',
            'episodeId' => 2,
            'podcastId' => 1

        ]);


        $response = $this->json('get', '/api/episodeRecord');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                [],[],[],[],[],[],[]
            ]);

    }

    /**
 * Test when the database has only 1 matching result
 *
 * @return void
 * @test
 */
    public function testResponseWithSingleData()
    {
        //first day to be included
        $today = date("Y-m-d", strtotime("-0 Days"));

        //all the values apart from the date are arbitrary and can be anything
        //the reason I do not let the faker assign random values to them is to be able to use assertExactJson
        factory(EpisodeDownload::class)->create([
            'occurredAt' => $today . ' 08:19:58',
            'eventId' => 'something',
            'episodeId' => 2,
            'podcastId' => 1

        ]);


        $response = $this->json('get', '/api/episodeRecord');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                [
                    ['occurredAt' => $today . ' 08:19:58',
                        'eventId' => 'something',
                        'episodeId' => 2,
                        'podcastId' => 1]
                ],
                [],
                [],
                [],
                [],
                [],
                []
            ]);

    }

    /**
     * Test when the database has multiple matching results all in one day
     *
     * @return void
     * @test
     */
    public function testResponseWithMultipleDataInADay()
    {
        //a day between today and 6 days ago
        $currentDay = date("Y-m-d", strtotime("-2 Days"));

        //all the values apart from the date are arbitrary and can be anything
        //the reason I do not let the faker assign random values to them is to be able to use assertExactJson
        factory(EpisodeDownload::class)->create([
            'occurredAt' => $currentDay . ' 08:08:08',
            'eventId' => 'something2',
            'episodeId' => 42,
            'podcastId' => 14

        ]);
        factory(EpisodeDownload::class)->create([
            'occurredAt' => $currentDay . ' 09:09:09',
            'eventId' => 'something3',
            'episodeId' => 242,
            'podcastId' => 134

        ]);


        $response = $this->json('get', '/api/episodeRecord');

        //{"eventId":"something","episodeId":2,"podcastId":1,"occurredAt":"2020-02-13 08:19:58"}
        $response
            ->assertStatus(200)
            ->assertExactJson([
                [],
                [],
                [
                    ['occurredAt' => $currentDay . ' 08:08:08',
                        'eventId' => 'something2',
                        'episodeId' => 42,
                        'podcastId' => 14],
                    ['occurredAt' => $currentDay . ' 09:09:09',
                        'eventId' => 'something3',
                        'episodeId' => 242,
                        'podcastId' => 134]
                ],
                [],
                [],
                [],
                []
            ]);

    }

    /**
     * Test when the database has multiple matching results in multiple days
     *
     * @return void
     * @test
     */
    public function testResponseWithMultipleDataMultipleDays()
    {
        //a day between today and 6 days ago
        $currentDay = date("Y-m-d", strtotime("-2 Days"));
        //first day to be included
        $today = date("Y-m-d", strtotime("-0 Days"));

        //all the values apart from the date are arbitrary and can be anything
        //the reason I do not let the faker assign random values to them is to be able to use assertExactJson
        factory(EpisodeDownload::class)->create([
            'occurredAt' => $today . ' 08:19:58',
            'eventId' => 'something',
            'episodeId' => 2,
            'podcastId' => 1

        ]);
        factory(EpisodeDownload::class)->create([
            'occurredAt' => $currentDay . ' 08:08:08',
            'eventId' => 'something2',
            'episodeId' => 42,
            'podcastId' => 14

        ]);
        factory(EpisodeDownload::class)->create([
            'occurredAt' => $currentDay . ' 09:09:09',
            'eventId' => 'something3',
            'episodeId' => 242,
            'podcastId' => 134

        ]);


        $response = $this->json('get', '/api/episodeRecord');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                [
                    ['occurredAt' => $today . ' 08:19:58',
                        'eventId' => 'something',
                        'episodeId' => 2,
                        'podcastId' => 1]
                ],
                [],
                [
                    ['occurredAt' => $currentDay . ' 08:08:08',
                        'eventId' => 'something2',
                        'episodeId' => 42,
                        'podcastId' => 14],
                    ['occurredAt' => $currentDay . ' 09:09:09',
                        'eventId' => 'something3',
                        'episodeId' => 242,
                        'podcastId' => 134]
                ],
                [],
                [],
                [],
                []
            ]);

    }

    /**
     * Test when the database has matching results for all days of the week and data to be excluded(old data)
     *
     * @return void
     * @test
     */
    public function testResponseWithMultipleDataInEveryDay()
    {
        //have at least 1 for every day and some older data too
        for ($x = 0; $x < 10; $x++) {
            $current = date("Y-m-d", strtotime("-" . $x . " Days"));
            factory(EpisodeDownload::class)->create([
                'occurredAt' => $current . ' 00:00:00',
                'eventId' => 'something0',
                'episodeId' => 0,
                'podcastId' => 0

            ]);
        }

        //a day between today and 6 days ago
        $currentDay = date("Y-m-d", strtotime("-2 Days"));
        //first day to be included
        $today = date("Y-m-d", strtotime("-0 Days"));

        //all the values apart from the date are arbitrary and can be anything
        //the reason I do not let the faker assign random values to them is to be able to use assertExactJson
        factory(EpisodeDownload::class)->create([
            'occurredAt' => $today . ' 08:19:58',
            'eventId' => 'something',
            'episodeId' => 2,
            'podcastId' => 1

        ]);
        factory(EpisodeDownload::class)->create([
            'occurredAt' => $currentDay . ' 08:08:08',
            'eventId' => 'something2',
            'episodeId' => 42,
            'podcastId' => 14

        ]);
        factory(EpisodeDownload::class)->create([
            'occurredAt' => $currentDay . ' 09:09:09',
            'eventId' => 'something3',
            'episodeId' => 242,
            'podcastId' => 134

        ]);


        $response = $this->json('get', '/api/episodeRecord');

        //{"eventId":"something","episodeId":2,"podcastId":1,"occurredAt":"2020-02-13 08:19:58"}
        $response
            ->assertStatus(200)
            ->assertExactJson([
                [
                    ['occurredAt' => date("Y-m-d", strtotime("-0 Days")) . ' 00:00:00',
                        'eventId' => 'something0',
                        'episodeId' => 0,
                        'podcastId' => 0],
                    ['occurredAt' => $today . ' 08:19:58',
                        'eventId' => 'something',
                        'episodeId' => 2,
                        'podcastId' => 1]
                ],
                [
                    ['occurredAt' => date("Y-m-d", strtotime("-1 Days")) . ' 00:00:00',
                        'eventId' => 'something0',
                        'episodeId' => 0,
                        'podcastId' => 0]
                ],
                [
                    ['occurredAt' => date("Y-m-d", strtotime("-2 Days")) . ' 00:00:00',
                        'eventId' => 'something0',
                        'episodeId' => 0,
                        'podcastId' => 0],
                    ['occurredAt' => $currentDay . ' 08:08:08',
                        'eventId' => 'something2',
                        'episodeId' => 42,
                        'podcastId' => 14],
                    ['occurredAt' => $currentDay . ' 09:09:09',
                        'eventId' => 'something3',
                        'episodeId' => 242,
                        'podcastId' => 134]
                ],
                [
                    ['occurredAt' => date("Y-m-d", strtotime("-3 Days")) . ' 00:00:00',
                        'eventId' => 'something0',
                        'episodeId' => 0,
                        'podcastId' => 0]
                ],
                [
                    ['occurredAt' => date("Y-m-d", strtotime("-4 Days")) . ' 00:00:00',
                        'eventId' => 'something0',
                        'episodeId' => 0,
                        'podcastId' => 0]
                ],
                [
                    ['occurredAt' => date("Y-m-d", strtotime("-5 Days")) . ' 00:00:00',
                        'eventId' => 'something0',
                        'episodeId' => 0,
                        'podcastId' => 0]
                ],
                [
                    ['occurredAt' => date("Y-m-d", strtotime("-6 Days")) . ' 00:00:00',
                        'eventId' => 'something0',
                        'episodeId' => 0,
                        'podcastId' => 0]
                ]
            ]);

    }



    /* Tried to compare results from the api with a json response created here but apparently does not work
    /**
     * Test when the database has only 1 matching result
     *
     * @return void
     * @test
     * @codeCoverageIgnore
     * @ignore
     */
    /*public function test2ResponseWithSingleData()
    {
        //Create 45 entries by using factory (it will create events in last 14 days)
        foreach(range(1,45) as $x) {
            factory(EpisodeDownload::class)->create();
        }


        //Get the dates of the last 7 days in relation to today
        for ($x = 0; $x < 7; $x++) {
            $currentDay = date("Y-m-d", strtotime("-" . $x . " Days"));
            $results[$x] = DB::select("select * from episode_downloads where occurredAt like '$currentDay%' ", array(1));
        }

        $correctRespone = new JsonResponse($results);

        $response = $this->json('get', '/api/episodeRecord');


        assert(strcmp((string)$correctRespone, (string)$response));


    }*/
}
