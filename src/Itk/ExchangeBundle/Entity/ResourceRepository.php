<?php
/**
 * @file
 * Contains the Resource repository.
 */

namespace Itk\ExchangeBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class ResourceRepository
 * @package Itk\ExchangeBundle\Entity
 */
class ResourceRepository extends EntityRepository {
  /**
   * Get the entity manager.
   *
   * @return \Doctrine\ORM\EntityManager
   *   The EntityManager.
   */
  public function getEntityManager() {
    return $this->_em;
  }

  /**
   * Get a Resource by mail.
   *
   * @param $mail
   *   The mail.
   * @return object|null
   *   The Resource if found.
   */
  public function findOneByMail($mail) {
    return $this->findOneBy(
      array(
        'mail' => $mail
      )
    );
  }
}
