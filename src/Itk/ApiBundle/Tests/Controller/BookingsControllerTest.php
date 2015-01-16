<?php

namespace Itk\ApiBundle\Tests\Controller;

use Itk\ApiBundle\ExtendedWebTestCase;

class BookingsControllerTest extends ExtendedWebTestCase {

  /**
   * @TODO implement
   */
  public function testPostUserBooking() {
    /*
        $client = $this->baseSetup();

        $client->request('GET', '/api/users/1');
        $response = $client->getResponse();
        $user = json_decode($response->getContent());

        $client->request('GET', '/api/resources/1');
        $response = $client->getResponse();
        $resource1 = json_decode($response->getContent());

        $client->request('GET', '/api/resources/2');
        $response = $client->getResponse();
        $resource2 = json_decode($response->getContent());

        $booking = json_encode(array(
          "user" => $user,
          "resources" => array(
            array(
              'id' => 1
            ),
            array(
              'id' => 3
            )
          )
        ));

        $client->request('POST', '/api/bookings', array(), array(), array(), $booking);
        print_r($response = $client->getResponse());
        $this->assertEmptyResponse($response, 204);
    */
  }
}