<?php
/**
 * @file
 * This file is a part of the Itk ApiBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Itk\ApiBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Itk\ApiBundle\Entity\User;

/**
 * Class UsersService
 *
 * @package Itk\ApiBundle\Services
 */
class UsersService {
  protected $container;
  protected $doctrine;
  protected $em;

  /**
   * Constructor.
   *
   * @param Container $container
   */
  function __construct(Container $container) {
    $this->container = $container;

    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();
  }

  /**
   * Get the user with $id
   *
   * @param $id
   * @return array
   */
  public function getUser($id) {
    $user = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User')->findOneById($id);

    $status = $user ? 200 : 404;

    return array(
      'data' => $user,
      'status' => $status
    );
  }

  /**
   * Get all users
   *
   * @return array
   */
  public function getAllUsers() {
    $users = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User')->findAll();

    $status = $users ? 200 : 404;

    return array(
      'data' => $users,
      'status' => $status
    );
  }

  /**
   * Update the user.
   *  - only status and roles.
   *  - all other data is set on creation.
   *
   * @param $id
   * @param User $updatedUser
   * @return array
   */
  public function updateUser($id, User $updatedUser) {
    // Get the user.
    $result = $this->getUser($id);
    if ($result['status'] !== 200) {
      return $result;
    }

    $user = $result['data'];

    // Update status
    if ($updatedUser->getStatus() !== null) {
      $user->setStatus($updatedUser->getStatus());
    }

    // Update roles.
    if ($updatedUser->getRoles() !== null) {
      // Remove roles.
      foreach($user->getRoles() as $role) {
        if (!$updatedUser->getRoles()->contains($role)) {
          $user->removeRole($role);
        }
      }

      // Add roles.
      foreach($updatedUser->getRoles() as $role) {
        if (!$user->getRoles()->contains($role)) {
          // TODO: validate each Role object.

          $user->addRole($role);
        }
      }
    }

    // Update db.
    $this->em->flush();

    return array(
      'data' => $user,
      'status' => 200
    );
  }
}