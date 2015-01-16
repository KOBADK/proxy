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
  }
}