<?php

namespace Itk\ApiBundle\Tests\Controller;

use Itk\ApiBundle\ExtendedWebTestCase;

use Itk\ApiBundle\Entity\User;

class UserControllerTest extends ExtendedWebTestCase {
  /**
   * Test the user get action.
   */
  public function testGetAllUser() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/users');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }

  /**
   * Test the user get action, with non-existing user id.
   */
  public function testGetExistingUser() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/users/3');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * Test the user get action, with existing user id.
   */
  public function testGetNonExistingUser() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }

  /**
   * Test updating a non-existing user
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
   * Test updating a non-existing user
   */
  public function testUpdateIDmismatches() {
    $client = $this->baseSetup();

    $user = array(
      'id' => 2,
      'name' => 'Tester',
      'uuid' => '123',
      'mail' => 'asd@asd.asd',
      'status' => false
    );
    $client->request('PUT', '/api/users/3', array(), array(), array(), json_encode($user));
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 400);

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

  public function testUpdateSuccess() {
    $client = $this->baseSetup();

    $client->request('GET', '/api/users/1', array(), array(), array(), array());
    $response = $client->getResponse();
    $user = json_decode($response->getContent());
    $user->status = false;
    $client->request('PUT', '/api/users/1', array(), array(), array(), json_encode($user));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);
  }
}
