<?php
/**
 * @file
 * Contains test for Bookings controller for /admin.
 */

namespace Koba\AdminBundle\Tests\Controller;

use Koba\AdminBundle\Tests\ExtendedWebTestCase;

/**
 * Class BookingsControllerTest
 *
 * Tests for Booking
 *
 * @package Koba\AdminBundle\Tests\Controller
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
    $client->request('GET', '/admin/bookings');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }

  /**
   * Get 1 bookings:
   * - get all
   * Expect: 200, json response
   */
  public function testGetUserBookings() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/admin/users/1/bookings');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }
}
