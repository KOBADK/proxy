<?php
/**
 * @file
 * Contains Koba group service.
 */

namespace Koba\MainBundle\Services;

use Koba\MainBundle\EntityRepositories\UserRepository;
use Koba\MainBundle\EntityRepositories\GroupRepository;
use Koba\MainBundle\Entity\Group;

/**
 * Class GroupService
 *
 * @package Koba\MainBundle\Services
 */
class GroupService {
  protected $groupRepository;
  protected $userRepository;

  /**
   * Constructor.
   *
   * @param GroupRepository $groupRepository
   *   The group repository.
   * @param UserRepository $userRepository
   *   The user repository.
   */
  function __construct(GroupRepository $groupRepository, UserRepository $userRepository) {
    $this->groupRepository = $groupRepository;
    $this->userRepository = $userRepository;
  }

  /**
   * Get the group with $id
   *
   * @param integer $id
   *   Id of the group.
   *
   * @return Group
   *   The group found.
   *
   * @TODO: implement this!
   */
  public function getGroup($id) {
/*    $role = $this->rolesRepository->findOneById($id);

    if (!$role) {
      return $this->helperService->generateResponse(404, null, array('message' => 'role not found'));
    }

    return $this->helperService->generateResponse(200, $role);*/
  }

  /**
   * Get all groups.
   *
   * @return array
   *   Array of groups.
   *
   * @TODO: implement this!
   */
  public function getAllGroups() {
/*    $roles = $this->rolesRepository->findAll();

    return $this->helperService->generateResponse(200, $roles);*/
  }

  /**
   * Create a group.
   *
   * @param Group $group
   *   The group class to create. Should contain title and description.
   *
   * @return boolean
   *   Success?
   */
  public function createRole(Group $group) {
/*    $validation = $this->helperService->validateRole($role);
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

    return $this->helperService->generateResponse(204);*/
  }

  /**
   * Update a group.
   *
   * @param integer $id
   *   Group id.
   * @param Group $updatedGroup
   *   The updated group.
   *
   * @return boolean
   *   Success?
   */
  public function updateRole($id, Group $updatedGroup) {
/*    $role = $this->rolesRepository->findOneById($id);

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

    return $this->helperService->generateResponse(204);*/
  }
}
