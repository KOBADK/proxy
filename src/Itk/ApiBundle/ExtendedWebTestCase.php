<?php

namespace Itk\ApiBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Itk\ApiBundle\Entity\User;
use Itk\ApiBundle\Entity\Role;
use Doctrine\ORM\EntityManager;

class ExtendedWebTestCase extends WebTestCase {
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

    $this->assertJson($response->getContent());
  }

  /**
   * Clean the test database and setup base data for tests.
   *
   * @param EntityManager $em
   */
  protected function setupDatabase($em) {
    // Empty database tables.
    $connection = $em->getConnection();
    $platform   = $connection->getDatabasePlatform();
    $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_roles_resources', true));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_roles_users', true));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_booking', true));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_resource', true));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_role', true));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_user', true));
    $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');

    // Create user 1
    $user1 = new User();
    $user1->setUuid("user1");
    $user1->setName("Name 1");
    $user1->setMail("test1@test.test");
    $user1->setStatus(true);
    $em->persist($user1);

    // Create user 2
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

    $em->flush();
  }
}
