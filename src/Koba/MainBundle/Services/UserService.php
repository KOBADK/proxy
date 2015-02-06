<?php
/**
 * @file
 * Contains the user service.
 */

namespace Koba\MainBundle\Services;

use Koba\MainBundle\EntityRepositories\GroupRepository;
use Koba\MainBundle\EntityRepositories\UserRepository;

/**
 * Class UserService
 *
 * @package Koba\MainBundle\Services
 */
class UserService {
  protected $userRepository;
  protected $roleRepository;

  /**
   * Constructor.
   *
   * @param UserRepository $userRepository
   *   The user repository.
   * @paramGroupRepository $groupRepository
   *   The group repository.
   */
  function __construct(UserRepository $userRepository, GroupRepository $groupRepository) {
    $this->userRepository = $userRepository;
    $this->roleRepository = $groupRepository;
  }

  /**
   * Get a user with $id
   *
   * @param $id
   *   Id of the user.
   *
   * @return \Koba\MainBundle\Entity\User
   *   The user.
   */
  public function getUser($id) {
    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      // TODO: Throw exception.
    }

    return $user;
  }

  /**
   * Get a user by uniqueId
   *
   * @param $uniqueId
   *   Unique id of the user.
   *
   * @return \Koba\MainBundle\Entity\User
   *   The user.
   */
  public function getUserByUniqueId($uniqueId) {
    $user = $this->userRepository->findOneByUniqueId($uniqueId);

    if (!$user) {
      // TODO: Throw exception.
    }

    return $user;
  }

  /**
   * Get all users
   *
   * @return array
   *   Array of users.
   */
  public function getAllUsers() {
    return $this->userRepository->findAll();
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
/*    $user = $this->userRepository->findOneById($id);

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

    return $this->helperService->generateResponse(204);*/
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
/*    $user = $this->userRepository->findOneById($userId);

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

    return $this->helperService->generateResponse(204);*/
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
/*    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      return $this->helperService->generateResponse(404, null, array('message' => 'user not found'));
    }

    return $this->helperService->generateResponse(200, $user->getBookings());*/
  }
}
