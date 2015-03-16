<?php
/**
 * @file
 * Contains the Apikey repository.
 */

namespace Koba\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class ApiKeyRepository
 * @package Koba\MainBundle\Entity
 */
class ApiKeyRepository extends EntityRepository {

  public function findOneByApiKey($apiKey) {
    return $this->findOneBy(
      array(
        'apiKey' => $apiKey
      )
    );
  }
}
