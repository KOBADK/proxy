<?php
/**
 * @file
 * @todo Missing file description?
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
    $this->helperService = $helperService;

    // @todo: The service is only dependent on the container to get the entity
    // manager?
    $this->container = $container;

    // @TODO: Inject "EntityManager $em" -> "@doctrine.orm.entity_manager" so
    // it's not dependent on doctrine inside the service.
    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();

    $this->rolesRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Role');
    $this->userRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User');
  }

  /**
   * Get the role with $id
   *
   * @param $id
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
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
   *   @TODO Missing description?
   */
  public function getAllRoles() {
    $roles = $this->rolesRepository->findAll();

    return $this->helperService->generateResponse(200, $roles);
  }

  /**
   * Create a role.
   *
   * @param Role $role The role class to create. Should contain title and description.
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
   */
  public function createRole(Role $role) {
    $validation = $this->helperService->validateRole($role);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    if ($this->rolesRepository->findOneByTitle($role->getTitle())) {
      return $this->helperService->generateResponse(409, null, array('message' => 'a role with that title already exists'));
    }

    // Persist the new role.
    $this->em->persist($role);

    // Update db.
    $this->em->flush();

    return $this->helperService->generateResponse(204);
  }

  /**
   * Update a role.
   *
   * @param $id
   *   @TODO Missing description?
   * @param Role $updatedRole
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
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
