<?php

namespace Itk\ApiBundle\Tests\Controller;

use Itk\ApiBundle\ExtendedWebTestCase;

class RolesControllerTest extends ExtendedWebTestCase {
  /**
   * GetAll:
   * - get all
   * Expect: 200, json response
   */
  public function testGetAllRoles() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/roles');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(5, count($array));
  }

  /**
   * Get existing role
   * Expect: 200
   */
  public function testGetRoleExists() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/roles/1');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }

  /**
   * Get non-existing role
   * Expect: 404
   */
  public function testGetRoleNotExists() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/roles/100');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * Update role
   * Expect: 204
   */
  public function testPutRoleSuccess() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/roles/1');
    $response = $client->getResponse();
    $role = json_decode($response->getContent());

    $role->title = 'fiskfisk';
    $role->description = 'faksfaks';

    $client->request('PUT', '/api/roles/1', array(), array(), array(), json_encode($role));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);

    // Assert valid json response.
    $client->request('GET', '/api/roles/1');
    $response = $client->getResponse();
    $roleUpdated = json_decode($response->getContent());

    $this->assertEquals($role->title, $roleUpdated->title);
    $this->assertEquals($role->description, $roleUpdated->description);
  }


  /**
   * Update role errors
   * Expect: 400
   */
  public function testPutRoleErrorValidation() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/roles/1');
    $response = $client->getResponse();
    $role = json_decode($response->getContent());

    $role->title = null;

    $client->request('PUT', '/api/roles/1', array(), array(), array(), json_encode($role));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 400);
  }


  /**
   * Update role errors
   * Expect: 404
   */
  public function testPutRoleErrorNotFound() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/api/roles/1');
    $response = $client->getResponse();
    $role = json_decode($response->getContent());

    $client->request('PUT', '/api/roles/1000', array(), array(), array(), json_encode($role));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 404);
  }
}