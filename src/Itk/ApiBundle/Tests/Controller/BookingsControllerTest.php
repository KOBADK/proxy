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

  /**
   * PostBooking: success
   */
  public function testPostBooking() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1/bookings');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(0, count($array));

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

    $client->request('GET', '/api/users/1/bookings');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(1, count($array));
  }

  /**
   * PostBooking: User not found
   */
  public function testPostBookingErrorUserNotFound() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/resources/1');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $resource1 = json_decode($response->getContent());

    $booking = array(
      'user' => array('id' => 100),
      'resource' => $resource1,
      'start_datetime' => '123412341',
      'end_datetime' => '123414341',
      'subject' => 'success',
      'description' => 'a successful booking'
    );

    $client->request('POST', '/api/bookings', array(), array(), array(), json_encode($booking));
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * PostBooking: Resource not found
   */
  public function testPostBookingErrorBookingNotFound() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $user1 = json_decode($response->getContent());

    $booking = array(
      'user' => $user1,
      'resource' => array('id' => 100),
      'start_datetime' => '123412341',
      'end_datetime' => '123414341',
      'subject' => 'success',
      'description' => 'a successful booking'
    );

    $client->request('POST', '/api/bookings', array(), array(), array(), json_encode($booking));
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * PostBooking: Resource not found
   */
  public function testPostBookingErrorValidation() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $user1 = json_decode($response->getContent());

    $booking = array(
      'user' => null,
      'resource' => null,
      'start_datetime' => '123412341',
      'end_datetime' => '123414341',
      'subject' => 'success',
      'description' => 'a successful booking'
    );

    $client->request('POST', '/api/bookings', array(), array(), array(), json_encode($booking));
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 400);
  }
}