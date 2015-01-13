<?php

namespace Itk\ApiBundle\Tests\Controller;

use Itk\ApiBundle\ExtendedWebTestCase;

class UserControllerTest extends ExtendedWebTestCase {
  /**
   * Test the user get action.
   */
  public function testGetAllUser() {
    $client = static::createClient();
    $em = $client->getContainer()->get('doctrine')->getManager();

    $this->emptyDatabase($em);
    $this->setupData($em);

    // Assert valid json response.
    $client->request('GET', '/api/users');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }

  /**
   * Test the user get action, with non-existing user id.
   */
  public function testGetExistingUser() {
    $client = static::createClient();
    $em = $client->getContainer()->get('doctrine')->getManager();

    $this->emptyDatabase($em);
    $this->setupData($em);

    // Assert valid json response.
    $client->request('GET', '/api/users/3');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 404);
  }

  /**
   * Test the user get action, with existing user id.
   */
  public function testGetNonExistingUser() {
    $client = static::createClient();
    $em = $client->getContainer()->get('doctrine')->getManager();

    $this->emptyDatabase($em);
    $this->setupData($em);

    // Assert valid json response.
    $client->request('GET', '/api/users/1');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }
}
