<?php

namespace Itk\ApiBundle\Tests\Controller;

use Itk\ApiBundle\ExtendedWebTestCase;

/**
 * Class BookingsControllerTest
 *
 * Tests for Booking
 *
 * @package Itk\ApiBundle\Tests\Controller
 */
class BookingsControllerTest extends ExtendedWebTestCase {
  /**
   * GetAll:
   * - get all
   * Expect: 200, json response
   */
  public function testGetAllBookings() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/bookings');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }

  public function testBooking() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $user1 = json_decode($response->getContent());

    $client->request('GET', '/api/resources/1');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $resource1 = json_decode($response->getContent());

    $booking = array(
      'user' => $user1,
      'resource' => $resource1,
      'start_datetime' => '123412341',
      'end_datetime' => '123414341',
      'subject' => 'success',
      'description' => 'a successful booking'
    );

    $client->request('POST', '/api/bookings', array(), array(), array(), json_encode($booking));
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 201);
  }
}