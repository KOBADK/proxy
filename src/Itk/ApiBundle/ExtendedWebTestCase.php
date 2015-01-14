<?php

namespace Itk\ApiBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Itk\ApiBundle\Entity\User;
use Itk\ApiBundle\Entity\Role;
use Itk\ApiBundle\Entity\Resource;
use Doctrine\ORM\EntityManager;

class ExtendedWebTestCase extends WebTestCase {
  /**
   * @return \Symfony\Bundle\FrameworkBundle\Client
   */
  protected function baseSetup() {
    $client = static::createClient();
    $em = $client->getContainer()->get('doctrine')->getManager();

    $this->emptyDatabase($em);
    $this->setupData($em);

    return $client;
  }

  /**
   * Asserts an empty response.
   *
   * @param $response
   * @param int $statusCode
   */
  protected function assertEmptyResponse($response, $statusCode = 200) {
    $this->assertEquals(
      $statusCode, $response->getStatusCode()
    );
  }

  /**
   * Asserts a json response.
   *
   * @param $response
   * @param int $statusCode
   */
  protected function assertJsonResponse($response, $statusCode = 200) {
    $this->assertEquals(
      $statusCode, $response->getStatusCode(),
      $response->getContent()
    );
    $this->assertTrue(
      $response->headers->contains('Content-Type', 'application/json'),
      $response->headers
    );

    if ($response->getContent()) {
      $this->assertJson($response->getContent());
    }
  }

  /**
   * Clean the test database and setup base data for tests.
   *
   * @param EntityManager $em
   */
  protected function emptyDatabase($em) {
    // Empty database tables.
    $connection = $em->getConnection();
    $platform = $connection->getDatabasePlatform();
    $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_roles_resources', TRUE));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_roles_users', TRUE));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_booking', TRUE));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_resource', TRUE));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_role', TRUE));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_user', TRUE));
    $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
  }

  protected function setupData($em) {
    $user1 = new User();
    $user1->setUuid("user1");
    $user1->setName("Name 1");
    $user1->setMail("test1@test.test");
    $user1->setStatus(true);
    $em->persist($user1);

    $user2 = new User();
    $user2->setUuid("user2");
    $user2->setName("Name 2");
    $user2->setMail("test2@test.test");
    $user2->setStatus(true);
    $em->persist($user2);

    $role1 = new Role();
    $role1->setTitle('Anonym');
    $role1->setDescription('skal kunne se om ressourcer er bookede eller ledige uden information omkring hvorfor en given ressource er tilgængelig eller optaget.');
    $role1->addUser($user2);
    $em->persist($role1);

    $role2 = new Role();
    $role2->setTitle('CPR');
    $role2->setDescription('vil være den rolle en almindelig borger får tildelt ved login.');
    $role2->addUser($user1);
    $em->persist($role2);

    $role3 = new Role();
    $role3->setTitle('CVR');
    $role3->setDescription('vil være den rolle en virksomhed får tildelt ved login.');
    $em->persist($role3);

    $role4 = new Role();
    $role4->setTitle('Ansat');
    $role4->setDescription('vil være den rolle en ansat får tildelt ved login.');
    $em->persist($role4);

    $resource1 = new Resource();
    $resource1->setName("Rum 1");
    $resource1->setMail("test1@test.test");
    $resource1->setRouting("SMTP");
    $resource1->setMailbox("PublicDL");
    $resource1->setExpire(1000000);
    $resource1->addRole($role1);
    $resource1->addRole($role2);
    $resource1->addRole($role3);
    $resource1->addRole($role4);
    $em->persist($resource1);

    $resource2 = new Resource();
    $resource2->setName("Rum 2");
    $resource2->setMail("test2@test.test");
    $resource2->setRouting("SMTP");
    $resource2->setMailbox("PublicDL");
    $resource2->setExpire(1000000);
    $em->persist($resource2);

    $role5 = new Role();
    $role5->setTitle('Rum 2 adgang');
    $role5->setDescription('adgang til Rum 2');
    $role5->addResource($resource2);
    $em->persist($role5);

    $user2->addRole($role5);

    $em->flush();
  }
}
