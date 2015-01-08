<?php

namespace Itk\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TestControllerTest extends WebTestCase {
  protected function assertJsonResponse($response, $statusCode = 200) {
    $this->assertEquals(
      $statusCode, $response->getStatusCode(),
      $response->getContent()
    );
    $this->assertTrue(
      $response->headers->contains('Content-Type', 'application/json'),
      $response->headers
    );

    $this->assertJson($response->getContent());
  }

  protected function assertXmlResponse($response, $statusCode = 200) {
    $this->assertEquals(
      $statusCode, $response->getStatusCode(),
      $response->getContent()
    );
    $this->assertTrue(
      $response->headers->contains('Content-Type', 'text/xml; charset=UTF-8'),
      $response->headers
    );
  }

  public function testUserGet() {
    $client = static::createClient();

    // Assert valid XML response.
    $client->request('GET', '/api/test/user?_format=xml');
    $response = $client->getResponse();
    $this->assertXmlResponse($response, 200);

    // Assert valid json response.
    $client->request('GET', '/api/test/user?_format=json');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);

    // Assert valid json response.
    $client->request('GET', '/api/test/user');
    $response = $client->getResponse();
    $this->assertJsonResponse($response, 200);
  }
}
