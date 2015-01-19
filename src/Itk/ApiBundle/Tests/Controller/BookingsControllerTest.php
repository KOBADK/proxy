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
}