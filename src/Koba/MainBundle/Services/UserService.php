<?php
/**
 * @file
 * Contains the user service.
 */

namespace Koba\MainBundle\Services;

use Koba\MainBundle\Entity\GroupRepository;
use Koba\MainBundle\Entity\UserRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Koba\MainBundle\Entity\Group;

/**
 * Class UserService
 *
 * @package Koba\MainBundle\Services
 */
class UserService {
  protected $userRepository;
  protected $groupRepository;

  /**
   * Constructor.
   *
   * @param UserRepository $userRepository
   *   The user repository.
   * @param GroupRepository $groupRepository
   *   The group repository.
   */
  public function __construct(UserRepository $userRepository, GroupRepository $groupRepository) {
    $this->userRepository = $userRepository;
    $this->groupRepository = $groupRepository;
  }

  /**
   * Get a user with $id.
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
      throw new NotFoundHttpException('User not found.', NULL, 404);
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
      throw new NotFoundHttpException('User not found.', NULL, 404);
    }

    return $user;
  }

  /**
   * Get all users.
   *
   * @return array
   *   Array of users.
   */
  public function getAllUsers() {
    return $this->userRepository->findAll();
  }

  /**
   * Update a user's status
   *
   * @param integer $id user id
   *   Id of the user.
   * @param boolean $status user status
   *   Status.
   *
   * @return boolean
   *   Success?
   */
  public function setUserStatus($id, $status) {
    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      throw new NotFoundHttpException('User not found.', NULL, 404);
    }

    if (!is_bool($status)) {
      throw new ValidatorException('Status is not a boolean');
    }

    $user->setStatus($status);

    $this->userRepository->flush();

    return TRUE;
  }

  /**
   * Get a user's groups.
   *
   * @param $id
   *   Id of the user.
   * @return array
   *   Array of groups.
   */
  public function getUserGroups($id) {
    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      throw new NotFoundHttpException('User not found.');
    }

    return $user->getGroups();
  }

  /**
   * Add a group to a user
   *
   * @param integer $userId
   *   Id of the user.
   * @param Group $group
   *   Group to add.
   *
   * @return boolean
   *   Success?
   */
  public function addGroupToUser($userId, Group $group) {
    // @TODO: Fix validation.
    /*
    $validation = $this->helperService->validateRole($group);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], NULL, $validation['errors']);
    }
    */
    $user = $this->userRepository->findOneById($userId);

    if (!$user) {
      throw new NotFoundHttpException('User not found.', NULL, 404);
    }

    $group = $this->groupRepository->findOneById($group->getId());

    if (!$group) {
      throw new NotFoundHttpException('Group not found.', NULL, 404);
    }

    if ($user->getGroups()->contains($group)) {
      throw new HttpException(409, 'User already has that group');
    }

    $user->addGroup($group);
    $this->userRepository->flush();

    return TRUE;
  }

  /**
   * Remove a group from a user.
   *
   * @param $uid
   *   Id of the user.
   * @param $gid
   *   Id of the group.
   *
   * @return boolean
   *   Success?
   */
  public function removeGroupFromUser($uid, $gid) {
    $user = $this->userRepository->findOneById($uid);

    if (!$user) {
      throw new NotFoundHttpException('User not found.', NULL, 404);
    }

    $group = $this->groupRepository->findOneById($gid);

    if (!$group) {
      throw new NotFoundHttpException('Group not found.', NULL, 404);
    }

    if (!$user->getGroups()->contains($group)) {
      throw new HttpException(409, 'User does not have that group.');
    }

    $user->removeGroup($group);
    $this->userRepository->flush();

    return TRUE;
  }

  /**
   * Returns a user's bookings
   *
   * @param $id
   *   Id of the user.
   *
   * @return array
   *   Array of bookings.
   */
  public function getUserBookings($id) {
    $user = $this->userRepository->findOneById($id);

    if (!$user) {
      throw new NotFoundHttpException('User not found.', NULL, 404);
    }

    return $user->getBookings();
  }
}
