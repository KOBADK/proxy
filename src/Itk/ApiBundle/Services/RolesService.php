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
  protected $helperService;
  protected $rolesRepository;
  protected $userRepository;

  /**
   * Constructor.
   *
   * @param Container $container
   * @param HelperService $helperService
   */
  function __construct(Container $container, HelperService $helperService) {
    $this->container = $container;
    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();
    $this->helperService = $helperService;
    $this->rolesRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Role');
    $this->userRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User');
  }

  /**
   * Get the role with $id
   *
   * @param $id
   * @return array
   */
  public function getRole($id) {
    $role = $this->rolesRepository->findOneById($id);

    if (!$role) {
      return $this->helperService->generateResponse(404, null, array('message' => 'role not found'));
    }

    return $this->helperService->generateResponse(200, $role);
  }

  /**
   * Get all roles
   *
   * @return array
   */
  public function getAllRoles() {
    $roles = $this->rolesRepository->findAll();

    return $this->helperService->generateResponse(200, $roles);
  }

  /**
   * Create a role.
   * @param Role $role
   * @return array
   */
  public function createRole(Role $role) {
    $validation = $this->helperService->validateRole($role);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    // Create the new role.
    $newRole = new Role();
    $newRole->setTitle($role->getTitle());
    $newRole->setDescription($role->getDescription());

    // Set users.
    if ($newRole->getUsers() !== null) {
      // Add users.
      foreach($role->getUsers() as $user) {
        $user = $this->userRepository->findOneById($user->getId());
        $newRole->addUser($user);
      }
    }

    $this->em->persist($newRole);

    // Update db.
    $this->em->flush();

    return $this->helperService->generateResponse(204);
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
    $role = $this->rolesRepository->findOneById($id);

    $validation = $this->helperService->validateRole($updatedRole);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, array('stuff' => $validation['errors']));
    }

    if (!$role) {
      return $this->helperService->generateResponse(404, null, array('message' => 'role not found'));
    }

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
        $user = $this->userRepository->findOneById($user->getId());
        $role->addUser($user);
      }
    }

    // Update db.
    $this->em->flush();

    return $this->helperService->generateResponse(204);
  }
}