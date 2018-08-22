<?php
use PHPUnit\Framework\TestCase;

class UserTestCase extends TestCase
{
  private $http;

  public function setUp()
  {
    $this->http = new GuzzleHttp\Client(['base_uri' => 'http://localhost/conceptAPI/']);
  }

  public function testCarGet()
  {
    $response = $this->http->request('GET', 'car');
    $this->assertEquals(200, $response->getStatusCode());
    $contentType = $response->getHeaders()["Content-Type"][0];
    $this->assertEquals("application/json", $contentType);

    $resultExpected = false;
    //equivalence class 1 of CarCreation use case: creation with not all required attributes
    try{
      $response = $this->http->post('car', [
        'headers' => [
          'Content-Type' => 'application/json',
        ],
        "body" =>json_encode([
          "model" => "BMW"
        ])
      ]);
    }catch(GuzzleHttp\Exception\ClientException $exception){
      $resultExpected = true;
      $this->assertEquals(422, $exception->getResponse()->getStatusCode());
    }
    $this->assertTrue($resultExpected);
    $resultExpected = false;

    //equivalence class 2 of CarCreation use case: creation with all required attributes and good type fields
    $response = $this->http->post('car', array(
      'headers' => [
        'Content-Type' => 'application/json',
        ],
        "body" =>json_encode([
          "model" => "Spider",
          "engine": "2.4",
          "current_location": 0,
          "number_of_seats": "2",
          "number_of_doors": "3",
          "fuel_type": "Gasoline",
          "plate_number": "111555222",
          "infotainment_system": "none",
          "interior_design" : "sport"
          ])
        ));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        //CarUpdate use case -> equivalence class 1 CarID not present
        try{
          $response = $this->http->put('car', array(
            'headers' => [
              'Content-Type' => 'application/json',
              ],
              "body" =>json_encode([
                "carID" => "-1",
                "current_location" : 6
                ])
              ));
        }catch(GuzzleHttp\Exception\ClientException $exception){
          $resultExpected = true;
          $this->assertEquals(404, $exception->getResponse()->getStatusCode());
        }
        $this->assertTrue($resultExpected);
        $resultExpected = false;

        //CarUpdate use case: equivalence class 3 CarID present
        $response = $this->http->put('car', array(
          'headers' => [
            'Content-Type' => 'application/json',
            ],
            "body" =>json_encode([
              "carID" => "0",
              "current_location" => 82
              ])
            ));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());

        //assert change
        $response = $this->http->get('car/1');
        $this->assertEquals(json_decode($response->getBody(), true)['model'], 'BMW');

      }

      public function tearDown() {
        $this->http = null;
      }
    }
