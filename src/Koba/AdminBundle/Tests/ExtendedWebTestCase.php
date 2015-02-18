<?php
/**
 * @file
 * Contains the ExtendedWebTestCase class.
 */

namespace Koba\AdminBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Koba\MainBundle\Entity\User;
use Koba\MainBundle\Entity\Group;
use Koba\MainBundle\Entity\Resource;
use Doctrine\ORM\EntityManager;

class ExtendedWebTestCase extends WebTestCase {
  /**
   * Make base database setup.
   *
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
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_groups_resources', TRUE));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_groups_users', TRUE));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_booking', TRUE));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_resource', TRUE));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_group', TRUE));
    $connection->executeUpdate($platform->getTruncateTableSQL('koba_user', TRUE));
    $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
  }

  /**
   * Data setup for tests.
   *
   * @param $em
   */
  protected function setupData($em) {
    $user1 = new User();
    $user1->setUniqueId('user1');
    $user1->setName('Name 1');
    $user1->setMail('test1@test.test');
    $user1->setStatus(TRUE);
    $em->persist($user1);

    $user2 = new User();
    $user2->setUniqueId('user2');
    $user2->setName('Name 2');
    $user2->setMail('test2@test.test');
    $user2->setStatus(TRUE);
    $em->persist($user2);

    $group1 = new Group();
    $group1->setTitle('Anonym');
    $group1->setDescription('skal kunne se om ressourcer er bookede eller ledige uden information omkring hvorfor en given ressource er tilgængelig eller optaget.');
    $group1->addUser($user2);
    $em->persist($group1);

    $group2 = new Group();
    $group2->setTitle('CPR');
    $group2->setDescription('vil være den rolle en almindelig borger får tildelt ved login.');
    $group2->addUser($user1);
    $em->persist($group2);

    $group3 = new Group();
    $group3->setTitle('CVR');
    $group3->setDescription('vil være den rolle en virksomhed får tildelt ved login.');
    $em->persist($group3);

    $group4 = new Group();
    $group4->setTitle('Ansat');
    $group4->setDescription('vil være den rolle en ansat får tildelt ved login.');
    $em->persist($group4);

    $resource1 = new Resource();
    $resource1->setName('Rum 1');
    $resource1->setMail('test1@test.test');
    $resource1->setRouting('SMTP');
    $resource1->setMailbox('PublicDL');
    $resource1->setExpire(1000000);
    $resource1->addGroup($group1);
    $resource1->addGroup($group2);
    $resource1->addGroup($group3);
    $resource1->addGroup($group4);
    $em->persist($resource1);

    $resource2 = new Resource();
    $resource2->setName('Rum 2');
    $resource2->setMail('test2@test.test');
    $resource2->setRouting('SMTP');
    $resource2->setMailbox('PublicDL');
    $resource2->setExpire(1000000);
    $em->persist($resource2);

    $group5 = new Group();
    $group5->setTitle('Rum 2 adgang');
    $group5->setDescription('adgang til Rum 2');
    $group5->addResource($resource2);
    $em->persist($group5);

    $user2->addGroup($group5);

    $em->flush();
  }
}
