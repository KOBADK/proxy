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
use Itk\ApiBundle\Entity\Role;

/**
 * Class RolesService
 *
 * @package Itk\ApiBundle\Services
 */
class RolesService {
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
   * Get the role with $id
   *
   * @param $id
   * @return array
   */
  public function getRole($id) {
    $role = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Role')->findOneById($id);

    $status = $role ? 200 : 404;

    return array(
      'data' => $role,
      'status' => $status
    );
  }

  /**
   * Get all roles
   *
   * @return array
   */
  public function getAllRoles() {
    $roles = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Role')->findAll();

    $status = $roles ? 200 : 404;

    return array(
      'data' => $roles,
      'status' => $status
    );
  }

  /**
   * Create a role.
   * @param Role $role
   * @return array
   */
  public function createRole(Role $role) {
    // Validate input
    if ($role->getTitle() === null) {
      return array(
        'data' => null,
        'status' => 400
      );
    }

    // Create the new role.
    $newRole = new Role();
    $newRole->setTitle($role->getTitle());
    $newRole->setDescription($role->getDescription());

    // Set users.
    if ($newRole->getUsers() !== null) {
      // Add users.
      foreach($role->getUsers() as $user) {
        $user = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User')->findOneById($user->getId());
        $newRole->addUser($user);
      }
    }

    $this->em->persist($newRole);

    // Update db.
    $this->em->flush();

    return array(
      'data' => $newRole,
      'status' => 200
    );
  }

  /**
   * Update a role.
   *
   * @param $id
   * @param Role $updatedRole
   *
   * @return array
   */
  public function updateRole($id, Role $updatedRole) {
    // Get the role.
    $result = $this->getRole($id);
    if ($result['status'] !== 200) {
      return $result;
    }

    $role = $result['data'];

    // Update
    if ($updatedRole->getTitle() !== null) {
      $role->setTitle($updatedRole->getTitle());
    }

    if ($updatedRole->getDescription() !== null) {
      $role->setDescription($updatedRole->getDescription());
    }

    // Update users.
    if ($updatedRole->getUsers() !== null) {
      // Remove users.
      foreach($role->getUsers() as $user) {
        if (!$updatedRole->getUsers()->contains($role)) {
          $role->removeUser($user);
        }
      }

      // Add users.
      foreach($updatedRole->getUsers() as $user) {
        $user = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User')->findOneById($user->getId());
        $role->addUser($user);
      }
    }

    // Update db.
    $this->em->flush();

    return array(
      'data' => $role,
      'status' => 200
    );
  }
}