<?php
/**
 * @file
 * Contains test for Users controller for /admin.
 */

namespace Koba\AdminBundle\Tests\Controller;

use Koba\AdminBundle\Tests\ExtendedWebTestCase;

/**
 * Class UsersControllerTest
 *
 * Tests for UsersController
 *
 * @package Koba\AdminBundle\Tests\Controller
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
    $client->request('GET', '/admin/users');
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
    $client->request('GET', '/admin/users/3');
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
    $client->request('GET', '/admin/users/1');
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
      'status' => FALSE
    );
    $client->request('PUT', '/admin/users/3/status', array(), array(), array(), json_encode($status));
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
    $client->request('PUT', '/admin/users/2/status', array(), array(), array(), json_encode($status));
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

    $client->request('GET', '/admin/users/1');
    $response = $client->getResponse();
    $user = json_decode($response->getContent());
    $this->assertEquals(TRUE, $user->status);

    $status = array(
      'status' => FALSE
    );

    $client->request('PUT', '/admin/users/1/status', array(), array(), array(), json_encode($status));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);

    $client->request('GET', '/admin/users/1');
    $response = $client->getResponse();
    $user = json_decode($response->getContent());

    $this->assertEquals(FALSE, $user->status);
  }

  /**
   * Get User Groups: 3 cases
   * - Get user 1 groups
   *   Expect 200: 1 group
   * - Get user 2 groups
   *   Expect 200: 2 groups
   * - Get user 3
   *   Expect 404
   */
  public function testGetUserGroups() {
    $client = $this->baseSetup();

    $client->request('GET', '/admin/users/1/groups');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);

    $array = (array) json_decode($response->getContent());
    $this->assertEquals(1, count($array));

    $client->request('GET', '/admin/users/2/groups');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);

    $array = (array) json_decode($response->getContent());
    $this->assertEquals(2, count($array));

    $client->request('GET', '/admin/users/3/groups');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * Post User Group:
   * - get user 1 groups
   * - get group 5
   * - add group 5 to user 1
   * - check that the group has been added
   */
  public function testPostUserGroup() {
    $client = $this->baseSetup();

    $client->request('GET', '/admin/users/1/groups');
    $response = $client->getResponse();
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(1, count($array));

    $client->request('GET', '/admin/groups/5');
    $group = $client->getResponse()->getContent();

    $client->request('POST', '/admin/users/1/groups', array(), array(), array(), $group);
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);

    $client->request('GET', '/admin/users/1/groups');
    $response = $client->getResponse();
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(2, count($array));
  }

  /**
   * Delete user group
   * - get user 1 groups
   * - delete group 2
   * - check that the group has been deleted
   */
  public function testDeleteUserGroup() {
    $client = $this->baseSetup();

    $client->request('GET', '/admin/users/1/groups');
    $response = $client->getResponse();
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(1, count($array));

    $client->request('DELETE', '/admin/users/1/groups/2');
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);

    $client->request('GET', '/admin/users/1/groups');
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

    $client->request('GET', '/admin/users/1/bookings');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);

    $array = (array) json_decode($response->getContent());
    $this->assertEquals(0, count($array));
  }
}
