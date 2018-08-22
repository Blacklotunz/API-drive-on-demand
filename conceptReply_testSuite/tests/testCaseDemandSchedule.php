<?php
use PHPUnit\Framework\TestCase;

class DemandScheduleTestCase extends TestCase
{
  private $http;

  public function setUp()
  {
    $this->http = new GuzzleHttp\Client(['base_uri' => 'http://localhost/conceptAPI/']);
  }

  public function testDemandGet()
  {
    $response = $this->http->request('GET', 'demandzz');
    $this->assertEquals(200, $response->getStatusCode());
    $contentType = $response->getHeaders()["Content-Type"][0];
    $this->assertEquals("application/json", $contentType);
    $this->assertEquals("requested demandzz is not supported yet", $response->getBody()->getContents());

    $response = $this->http->request('GET', 'demand');
    $this->assertEquals(200, $response->getStatusCode());
    $contentType = $response->getHeaders()["Content-Type"][0];
    $this->assertEquals("application/json", $contentType);

    //Usecase DemandSchedule all scheduled
    $response = $this->http->request('GET', 'schedule.php', ['base_uri' => 'http://localhost/']);
    $responseBody = $response->getBody()->getContents();
    $this->AssertEquals(json_decode($responseBody,true)['unscheduled'], null);
    /////////

    $resultExpected = false;
    //equivalence class 1 of CarCreation use case: creation with not all required attributes
    try{
      $response = $this->http->post('demand', [
        'headers' => [
          'Content-Type' => 'application/json',
        ],
        "body" =>json_encode([
          "userID" => "1000"
        ])
      ]);

    }catch(GuzzleHttp\Exception\ClientException $exception){
      $resultExpected = true;
      $this->assertEquals(422, $exception->getResponse()->getStatusCode());
      $this->AssertEquals('Missing mandatory data', json_decode($exception->getResponse()->getBody()->getContents(), true)['message']);
    }
    $this->assertTrue($resultExpected);

    $resultExpected = false;



    $response = $this->http->post('demand', [
      'headers' => [
        'Content-Type' => 'application/json',
      ],
      "body" =>json_encode([
        "userID"=>"1",
        "desired_features"=>["model" => "Ferrari"],
        "start_time"=>"10:00",
        "end_time"=>"12:00",
        "pick_up_location"=>"12",
        "drop_off_location"=>"0"
      ])
    ])->getBody()->getContents();

    $this->AssertEquals(null, json_decode($response,true)['unscheduled']);




    //Usecase DemandSchedule unscheduled
    $response = $this->http->request('GET', 'schedule.php', ['base_uri' => 'http://localhost/']);
    $responseBody = $response->getBody()->getContents();
    $this->AssertEquals(json_decode($responseBody,true)['unscheduled'], null);


      }

      public function tearDown() {
        $this->http = null;
      }
    }
