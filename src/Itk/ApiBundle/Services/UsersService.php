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
use Itk\ApiBundle\Entity\Role;

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
   * @param Container $container
   */
  function __construct(Container $container, HelperService $helperService) {
    $this->container = $container;
    $this->helperService = $helperService;

    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();

    $this->userRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User');
    $this->roleRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Role');
  }

  /**
   * Get a user with $id
   *
   * @param $id
   * @return array
   */
  public function getUser($id) {
    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      return $this->helperService->generateResponse(404, null, array('message' => 'user not found'));
    }

    return $this->helperService->generateResponse(200, $user);
  }

  /**
   * Get all users
   *
   * @return array
   */
  public function getAllUsers() {
    $users = $this->userRepository->findAll();

    return $this->helperService->generateResponse(200, $users);
  }

  /**
   * Update a user
   *
   * @param $id
   * @param User $updatedUser
   * @return array
   */
  public function updateUser($id, User $updatedUser) {
    // Validate user
    $validation = $this->get('koba.helper_service')->validateUser($updatedUser);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    // Validate ids match
    if ($id != $updatedUser->getId()) {
      return $this->helperService->generateResponse(400, null, array('errors' => 'ids do not match'));
    }

    if (!$this->userRepository->findOneById($updatedUser->getId())) {
      return $this->helperService->generateResponse(404, null, array('errors' => 'user not found'));
    }

    $this->em->merge($updatedUser);
    $this->em->flush();

    return $this->helperService->generateResponse(204);
  }

  /**
   * Get a user's roles
   *
   * @param $id
   * @return array
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
   * @param Role $role
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
   * @param $roleId
   * @return array
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
   * @return array
   */
  public function getUserBookings($id) {
    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      return $this->helperService->generateResponse(404, null, array('message' => 'user not found'));
    }

    return $this->helperService->generateResponse(200, $user->getBookings());
  }
}