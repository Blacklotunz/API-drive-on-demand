<?php
use PHPUnit\Framework\TestCase;

class UserTestCase extends TestCase
{
  private $http;

  public function setUp()
  {
    $this->http = new GuzzleHttp\Client(['base_uri' => 'http://localhost/conceptAPI/']);
  }

  public function testUserGet()
  {
    $response = $this->http->request('GET', 'user');
    $this->assertEquals(200, $response->getStatusCode());
    $contentType = $response->getHeaders()["Content-Type"][0];
    $this->assertEquals("application/json", $contentType);

    $resultExpected = false;
    //equivalence class 1 of UserCreation use case: creation with not all required attributes
    try{
      $response = $this->http->post('user', [
        'headers' => [
          'Content-Type' => 'application/json',
        ],
        "body" =>json_encode([
          "name" => "test_user"
        ])
      ]);
    }catch(GuzzleHttp\Exception\ClientException $exception){
      $resultExpected = true;
      $this->assertEquals(422, $exception->getResponse()->getStatusCode());
    }
    $this->assertTrue($resultExpected);
    $resultExpected = false;

    //equivalence class 2 of UserCreation use case: creation with all required attributes and wrong type field
    try{
      $response = $this->http->post('user', [
        'headers' => [
          'Content-Type' => 'application/json',
        ],
        "body" =>json_encode([
          "name" => "test_user",
          "password" => "test_pwd",
          "mail" => "mail@test.com",
          "actual_location" => "20",
          "phone_number" => "222111222333",
          "age" => "bad_number"
        ])
      ]);
    }catch(GuzzleHttp\Exception\ClientException $exception){
      $resultExpected = true;
      $this->assertEquals(422, $exception->getResponse()->getStatusCode());
    }

    $this->assertTrue($resultExpected);
    $resultExpected = false;


    //equivalence class 3 of UserCreation use case: creation with all required attributes and good type fields
    $response = $this->http->post('user', array(
      'headers' => [
        'Content-Type' => 'application/json',
        ],
        "body" =>json_encode([
          "name" => "test_user",
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
        and so on for other equivalence classes (e.g. actual_location may be NaN)
        */

        //UserUpdate use case -> equivalence class 1 UserID not present
        try{
          $response = $this->http->put('user', array(
            'headers' => [
              'Content-Type' => 'application/json',
              ],
              "body" =>json_encode([
                "name" => "test_user",
                "password" => "test_pwd",
                "mail" => "mail@test.com",
                "actual_location" => "20",
                "phone_number" => "222111222333",
                "age" => 28
                ])
              ));
        }catch(GuzzleHttp\Exception\ClientException $exception){
          $resultExpected = true;
          $this->assertEquals(404, $exception->getResponse()->getStatusCode());
        }
        $this->assertTrue($resultExpected);
        $resultExpected = false;


            //UserUpdate use case: equivalence class 2 UserID not present or wrong type
            try{
              $response = $this->http->put('user', array(
                'headers' => [
                  'Content-Type' => 'application/json',
                  ],
                  "body" =>json_encode([
                    "userID" => "not_a_valid_user",
                    "name" => "test_user",
                    "password" => "test_pwd",
                    "mail" => "mail@test.com",
                    "actual_location" => "20",
                    "phone_number" => "222111222333",
                    "age" => 28
                    ])
                  ));
            }catch(GuzzleHttp\Exception\ClientException $exception){
              $resultExpected = true;
              $this->assertEquals(404, $exception->getResponse()->getStatusCode());
            }
            $this->assertTrue($resultExpected);
            $resultExpected = false;

            //UserUpdate use case: equivalence class 3 UserID present
            $response = $this->http->put('user', array(
              'headers' => [
                'Content-Type' => 'application/json',
                ],
                "body" =>json_encode([
                  "userID" => "0",
                  "phone_number" => "0000000"
                  ])
                ));

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals(1,json_decode($response->getBody()->getContents(), true)['field_changed']);

            //assert change
            $response = $this->http->get('user/0');
            $this->assertEquals(json_decode($response->getBody(), true)['phone_number'], '0000000');

      }

      public function tearDown() {
        $this->http = null;
      }
    }
