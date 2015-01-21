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
   * @param Role $role The role class to create. Should contain title and description.
   * @return array
   */
  public function createRole(Role $role) {
    $validation = $this->helperService->validateRole($role);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    if ($this->rolesRepository->findOneByTitle($role->getTitle())) {
      return $this->helperService->generateResponse(409, null, array('message' => 'a role with that title already exists'));
    }

    // Persist the new role
    $this->em->persist($role);

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

    if (!$role) {
      return $this->helperService->generateResponse(404, null, array('message' => 'role not found'));
    }

    $validation = $this->helperService->validateRole($updatedRole);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    if ($role->getId() !== $updatedRole->getId()) {
      return $this->helperService->generateResponse(400, null, array('message' => 'ids do not match'));
    }

    // Update db.
    $this->em->merge($updatedRole);
    $this->em->flush();

    return $this->helperService->generateResponse(204);
  }
}