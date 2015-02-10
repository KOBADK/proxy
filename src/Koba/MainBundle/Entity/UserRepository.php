<?php
/**
 * @file
 * Contains the user repository.
 */

namespace Koba\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 * @package Koba\MainBundle\Entity
 */
class UserRepository extends EntityRepository {
  /**
   * Call the entity manager flush.
   *
   * @TODO: Is this the correct way to do this?
   */
  public function flush() {
    $this->getEntityManager()->flush();
  }
}
