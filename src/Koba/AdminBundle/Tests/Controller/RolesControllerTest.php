<?php

namespace Koba\AdminBundle\Tests\Controller;

use Koba\AdminBundle\Tests\ExtendedWebTestCase;

/**
 * Class GroupsControllerTest
 *
 * Tests for GroupsController.
 *
 * @package Koba\AdminBundle\Tests\Controller
 */
class GroupsControllerTest extends ExtendedWebTestCase {
  /**
   * GetAll:
   * - get all
   * Expect: 200, json response
   */
  public function testGetAllGroups() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/admin/groups');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(5, count($array));
  }

  /**
   * Get existing group
   * Expect: 200
   */
  public function testGetGroupExists() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/admin/groups/1');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }

  /**
   * Get non-existing group
   * Expect: 404
   */
  public function testGetGroupNotExists() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/admin/groups/100');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * Update group
   * Expect: 204
   */
  public function testPutGroupSuccess() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/admin/groups/1');
    $response = $client->getResponse();
    $group = json_decode($response->getContent());

    $group->title = 'fiskfisk';
    $group->description = 'faksfaks';

    $client->request('PUT', '/admin/groups/1', array(), array(), array(), json_encode($group));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);

    // Assert valid json response.
    $client->request('GET', '/admin/groups/1');
    $response = $client->getResponse();
    $groupUpdated = json_decode($response->getContent());

    $this->assertEquals($group->title, $groupUpdated->title);
    $this->assertEquals($group->description, $groupUpdated->description);
  }


  /**
   * Update group errors
   * Expect: 400
   */
  public function testPutGroupErrorValidation() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/admin/groups/1');
    $response = $client->getResponse();
    $group = json_decode($response->getContent());

    $group->title = NULL;

    $client->request('PUT', '/admin/groups/1', array(), array(), array(), json_encode($group));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 400);
  }


  /**
   * Update group errors
   * Expect: 404
   */
  public function testPutGroupErrorNotFound() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/admin/groups/1');
    $response = $client->getResponse();
    $group = json_decode($response->getContent());

    $client->request('PUT', '/admin/groups/1000', array(), array(), array(), json_encode($group));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 404);
  }

  /**
   * Create group
   * Expect: 204
   */
  public function testPostGroupSuccess() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/admin/groups');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(5, count($array));

    $group = array(
      "title" => "fisk",
      "description" => "and stuff"
    );

    $client->request('POST', '/admin/groups', array(), array(), array(), json_encode($group));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 204);

    // Assert valid json response.
    $client->request('GET', '/admin/groups');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(6, count($array));

    // Assert valid json response.
    $client->request('GET', '/admin/groups/6');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }

  /**
   * Create group with title taken
   * Expect: 409 conflict
   */
  public function testPostGroupErrorDuplicate() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/admin/groups');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(5, count($array));

    $group = array(
      "title" => "Anonym",
      "description" => "and stuff"
    );

    $client->request('POST', '/admin/groups', array(), array(), array(), json_encode($group));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 409);

    // Assert valid json response.
    $client->request('GET', '/admin/groups');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(5, count($array));

    // Assert valid json response.
    $client->request('GET', '/admin/groups/6');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * Create group validation error
   * Expect: 400 validation error
   */
  public function testPostGroupErrorValidation() {
    $client = $this->baseSetup();

    // Assert valid json response.
    $client->request('GET', '/admin/groups');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(5, count($array));

    $group = array(
      "title" => NULL,
      "description" => "and stuff"
    );

    $client->request('POST', '/admin/groups', array(), array(), array(), json_encode($group));
    $response = $client->getResponse();
    $this->assertEmptyResponse($response, 400);

    // Assert valid json response.
    $client->request('GET', '/admin/groups');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
    $array = (array) json_decode($response->getContent());
    $this->assertEquals(5, count($array));

    // Assert valid json response.
    $client->request('GET', '/admin/groups/6');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }
}