<?php
/**
 * @file
 * @todo Missing file description?
 */

namespace Itk\ApiBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Itk\ApiBundle\Entity\User;
use Itk\ApiBundle\Entity\Role;
use Itk\ApiBundle\Entity\Booking;

/**
 * Class UsersService
 *
 * @package Itk\ApiBundle\Services
 */
class UsersService {
  protected $container;
  protected $doctrine;
  protected $em;
  protected $helperService;
  protected $userRepository;
  protected $roleRepository;

  /**
   * Constructor.
   *
   * @todo: there is a lot of generateResponse(404, null,.... mayby a new
   * generateError would be needed?
   *
   * @param \Symfony\Component\DependencyInjection\Container $container
   *   @TODO Missing description?
   * @param \Itk\ApiBundle\Services\HelperService $helperService
   *   @TODO Missing description?
   */
  function __construct(Container $container, HelperService $helperService) {
    $this->container = $container;
    $this->helperService = $helperService;

    // @TODO: Inject "EntityManager $em" -> "@doctrine.orm.entity_manager" so
    // it's not dependent on doctrine inside the service.
    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();

    $this->userRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User');
    $this->roleRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Role');
  }

  /**
   * Get a user with $id
   *
   * @param $id
   *   @TODO Missing description?
   * @return array
   *   @TODO Missing description?
   */
  public function getUser($id) {
    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      return $this->helperService->generateResponse(404, null, array('message' => 'user not found'));
    }

    return $this->helperService->generateResponse(200, $user);
  }

  /**
   * Get a user by uniqueId
   *
   * @param $uniqueId
   *   @TODO Missing description?
   * @return mixed
   *   @TODO Missing description?
   */
  public function getUserByUniqueId($uniqueId) {
    return $this->userRepository->findOneByUniqueId($uniqueId);
  }

  /**
   * Get all users
   *
   * @return array
   *   @TODO Missing description?
   */
  public function getAllUsers() {
    $users = $this->userRepository->findAll();

    return $this->helperService->generateResponse(200, $users);
  }

  /**
   * Get paginated result
   *
   * @return array
   */
  public function fetchUsers($offset = 0, $limit = 5)
  {
    $users = $this->userRepository->findBy(array(), null, $limit, $offset);

    return $this->helperService->generateResponse(200, $users);
  }

  /**
   * Update user status
   *
   * @param integer $id user id
   *   @TODO Missing description?
   * @param boolean $status user status
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
   */
  public function setUserStatus($id, $status) {
    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      return $this->helperService->generateResponse(404, null, array('errors' => 'user not found'));
    }

    if (!is_bool($status)) {
      return $this->helperService->generateResponse(400, null, array('errors' => 'status is not a boolean'));
    }

    $user->setStatus($status);

    $validation = $this->helperService->validateUser($user);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    $this->em->flush();

    return $this->helperService->generateResponse(204);
  }

  /**
   * Get a user's roles
   *
   * @param $id
   *   @TODO Missing description?
   * @return array
   *   @TODO Missing description?
   */
  public function getUserRoles($id) {
    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      return $this->helperService->generateResponse(404, null, array('message' => 'user not found'));
    }

    return $this->helperService->generateResponse(200, $user->getRoles());
  }

  /**
   * Add a role to a user
   *
   * @param integer $userId
   *   @TODO Missing description?
   * @param Role $role
   *   @TODO Missing description?
   *
   * @return array
   */
  public function addRoleToUser($userId, $role) {
    $validation = $this->helperService->validateRole($role);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    $user = $this->userRepository->findOneById($userId);

    if (!$user) {
      return $this->helperService->generateResponse(404, null, array('message' => 'user not found'));
    }

    $role = $this->roleRepository->findOneById($role->getId());

    if (!$role) {
      return $this->helperService->generateResponse(404, null, array('message' => 'role not found'));
    }

    if ($user->getRoles()->contains($role)) {
      return $this->helperService->generateResponse(409, null, array('message' => 'user already has that role'));
    }

    $user->addRole($role);
    $this->em->flush();

    return $this->helperService->generateResponse(204);
  }

  /**
   * Remove a role from a user
   *
   * @param $userId
   *   @TODO Missing description?
   * @param $roleId
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
   */
  public function removeRoleFromUser($userId, $roleId) {
    $user = $this->userRepository->findOneById($userId);

    if (!$user) {
      return $this->helperService->generateResponse(404, null, array('message' => 'user not found'));
    }

    $role = $this->roleRepository->findOneById($roleId);

    if (!$role) {
      return $this->helperService->generateResponse(404, null, array('message' => 'role not found'));
    }

    if (!$user->getRoles()->contains($role)) {
      return $this->helperService->generateResponse(409, null, array('message' => 'user does not have that role'));
    }

    $user->removeRole($role);
    $this->em->flush();

    return $this->helperService->generateResponse(204);
  }

  /**
   * Returns a user's bookings
   *
   * @param $id
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
   */
  public function getUserBookings($id) {
    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      return $this->helperService->generateResponse(404, null, array('message' => 'user not found'));
    }

    return $this->helperService->generateResponse(200, $user->getBookings());
  }
}
