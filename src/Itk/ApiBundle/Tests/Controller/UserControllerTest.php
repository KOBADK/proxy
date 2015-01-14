<?php

namespace Itk\ApiBundle\Tests\Controller;

use Itk\ApiBundle\ExtendedWebTestCase;

class UserControllerTest extends ExtendedWebTestCase {
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

    $user = array(
      'id' => 3,
      'name' => 'Tester',
      'uuid' => '123',
      'mail' => 'asd@asd.asd',
      'status' => false
    );
    $client->request('PUT', '/api/users/3', array(), array(), array(), json_encode($user));
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * Update: id mismatch
   * Expect: 400
   */
  public function testUpdateIDmismatches() {
    $client = $this->baseSetup();

    $user = array(
      'id' => 2,
      'name' => 'Tester',
      'uuid' => '123',
      'mail' => 'asd@asd.asd',
      'status' => FALSE
    );
    $client->request('PUT', '/api/users/3', array(), array(), array(), json_encode($user));
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 400);
  }

  /**
   * Update: update success
   * - get user 1
   * - update fields
   * - get user 1
   * - assert changes have been made
   * Expect: 204 no content
   */
  public function testUpdateSuccess() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $user = json_decode($response->getContent());
    $user->uuid = "321";
    $user->status = 0;
    $user->name = "testertester";
    $user->mail = "t@t.t";

    $client->request('PUT', '/api/users/1', array(), array(), array(), json_encode($user));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);

    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $user = json_decode($response->getContent());

    $this->assertEquals(false, $user->status);
    $this->assertEquals("321", $user->uuid);
    $this->assertEquals("testertester", $user->name);
    $this->assertEquals("t@t.t", $user->mail);
  }

  /**
   * Update: invalid mail
   * - set wrong mail
   * - try update
   * - assert mail has not changed
   * Expect: 400 validation error
   */
  public function testUpdateInvalidMail() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $user = json_decode($response->getContent());
    $mail = $user->mail;

    // invalid update
    $user->mail = "ttttt";

    $client->request('PUT', '/api/users/1', array(), array(), array(), json_encode($user));
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 400);

    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $user = json_decode($response->getContent());
    $this->assertEquals($mail, $user->mail);
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
   * Get User Bookings:
   * - Get user 1 bookings
   *   Expect 200: 0 bookings
   *
   * @TODO: Expand test setup with bookings.
   */
  public function testGetUserBookings() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1/bookings');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);

    $array = (array) json_decode($response->getContent());
    $this->assertEquals(0, count($array));

  }
}
