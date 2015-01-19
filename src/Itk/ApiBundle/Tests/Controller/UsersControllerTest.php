<?php

namespace Itk\ApiBundle\Tests\Controller;

use Itk\ApiBundle\ExtendedWebTestCase;

/**
 * Class UsersControllerTest
 *
 * Tests for UsersController
 *
 * @package Itk\ApiBundle\Tests\Controller
 */
class UsersControllerTest extends ExtendedWebTestCase {
  /**
   * GetAll:
   * - get all
   * Expect: 200, json response
   */
  public function testGetAllUser() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/users');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }

  /**
   * GetAll:
   * - get user 3 (non existing)
   * Expect: 404, json response
   */
  public function testGetExistingUser() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/users/3');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * GetAll:
   * - get user 1
   * Expect: 200, json response
   */
  public function testGetNonExistingUser() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }

  /**
   * Update: updating a non-existing user
   * - updating user 3 (non-existing)
   * Expect: 404
   */
  public function testUpdateNonExistingUser() {
    $client = $this->baseSetup();

    $status = array(
      'status' => false
    );
    $client->request('PUT', '/api/users/3/status', array(), array(), array(), json_encode($status));
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * Update: updating a non-existing user
   * - updating user 3 (non-existing)
   * Expect: 404
   */
  public function testUpdateInvalid() {
    $client = $this->baseSetup();

    $status = array(
      'status' => array(
        'fisk' => 'faks'
      )
    );
    $client->request('PUT', '/api/users/2/status', array(), array(), array(), json_encode($status));
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 400);
  }

  /**
   * Update status: success
   * - get user 1
   * - update status
   * - get user 1
   * - assert changes have been made
   * Expect: 204 no content
   */
  public function testUpdateSuccess() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $user = json_decode($response->getContent());
    $this->assertEquals(true, $user->status);

    $status = array(
      'status' => false
    );

    $client->request('PUT', '/api/users/1/status', array(), array(), array(), json_encode($status));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);

    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $user = json_decode($response->getContent());

    $this->assertEquals(false, $user->status);
  }

  /**
   * Get User Roles: 3 cases
   * - Get user 1 roles
   *   Expect 200: 1 role
   * - Get user 2 roles
   *   Expect 200: 2 roles
   * - Get user 3
   *   Expect 404
   */
  public function testGetUserRoles() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1/roles');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);

    $array = (array) json_decode($response->getContent());
    $this->assertEquals(1, count($array));

    $client->request('GET', '/api/users/2/roles');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);

    $array = (array) json_decode($response->getContent());
    $this->assertEquals(2, count($array));

    $client->request('GET', '/api/users/3/roles');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * Post User Role:
   * - get user 1 roles
   * - get role 5
   * - add role 5 to user 1
   * - check that the role has been added
   */
  public function testPostUserRole() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1/roles');
    $response = $client->getResponse();
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(1, count($array));

    $client->request('GET', '/api/roles/5');
    $role = $client->getResponse()->getContent();

    $client->request('POST', '/api/users/1/roles', array(), array(), array(), $role);
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);

    $client->request('GET', '/api/users/1/roles');
    $response = $client->getResponse();
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(2, count($array));
  }

  /**
   * Delete user role
   * - get user 1 roles
   * - delete role 2
   * - check that the role has been deleted
   */
  public function testDeleteUserRole() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1/roles');
    $response = $client->getResponse();
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(1, count($array));

    $client->request('DELETE', '/api/users/1/roles/2');
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);

    $client->request('GET', '/api/users/1/roles');
    $response = $client->getResponse();
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(0, count($array));
  }

  /**
   * Get User Bookings:
   * - Get user 1 bookings
   *   Expect 200: 2 bookings
   */
  public function testGetUserBookings() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1/bookings');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);

    $array = (array) json_decode($response->getContent());
    $this->assertEquals(2, count($array));
  }
}
