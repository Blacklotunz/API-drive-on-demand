<?php
use PHPUnit\Framework\TestCase;

class CarTestCase extends TestCase
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
/*
    $contentType = $response->getHeaders()["Content-Type"][0];
    $this->assertEquals("application/json", $contentType);

    //equivalence class 1 of CarCreation use case: creation with not all required attributes
    try{
      $response = $this->http->post('car', [
        'headers' => [
          'Content-Type' => 'application/json',
        ],
        "body" =>json_encode([
          "name" => "test_car"
        ])
      ]);
    }catch(GuzzleHttp\Exception\ClientException $exception){
      $this->assertEquals(422, $exception->getResponse()->getStatusCode());
    }

    //equivalence class 2 of CarCreation use case: creation with all required attributes and wrong type field
    try{
      $response = $this->http->post('car', [
        'headers' => [
          'Content-Type' => 'application/json',
        ],
        "body" =>json_encode([
          "name" => "test_car",
          "password" => "test_pwd",
          "mail" => "mail@test.com",
          "actual_location" => "20",
          "phone_number" => "222111222333",
          "age" => "bad_number"
        ])
      ]);
    }catch(GuzzleHttp\Exception\ClientException $exception){
      $this->assertEquals(422, $exception->getResponse()->getStatusCode());
    }

/*
    //equivalence class 3 of CarCreation use case: creation with all required attributes and good type fields
    $response = $this->http->post('car', array(
      'headers' => [
        'Content-Type' => 'application/json',
        ],
        "body" =>json_encode([
          "name" => "test_car",
          "password" => "test_pwd",
          "mail" => "mail@test.com",
          "actual_location" => "20",
          "phone_number" => "222111222333",
          "age" => 28
          ])
        ));

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getBody());
        /*
        var_dump(json_decode($response->getBody()));die;
        $carAgent = json_decode($response->getBody());
        $this->assertRegexp('/Guzzle/', $carAgent);
        */
      }

      public function tearDown() {
        $this->http = null;
      }
    }
