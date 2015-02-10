<?php
/**
 * @file
 * Contains Koba group service.
 */

namespace Koba\MainBundle\Services;

use Koba\MainBundle\Entity\UserRepository;
use Koba\MainBundle\Entity\GroupRepository;
use Koba\MainBundle\Entity\Group;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\NotImplementedException;

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
  public function __construct(GroupRepository $groupRepository, UserRepository $userRepository) {
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
   */
  public function getGroup($id) {
    $group = $this->groupRepository->findOneById($id);

    if (!$group) {
      throw new NotFoundHttpException('Group not found', NULL, 404);
    }

    return $group;
  }

  /**
   * Get all groups.
   *
   * @return array
   *   Array of groups.
   */
  public function getAllGroups() {
    return $this->groupRepository->findAll();
  }

  /**
   * Create a group.
   *
   * @param Group $group
   *   The group class to create. Should contain title and description.
   *
   * @return boolean
   *   Success?
   *
   * @TODO: Implement this!
   */
  public function createGroup(Group $group) {
    throw new HttpException(500, 'Method not implemented.');

    /*    $validation = $this->helperService->validateRole($role);
        if ($validation['status'] !== 200) {
          return $this->helperService->generateResponse($validation['status'], NULL, $validation['errors']);
        }

        if ($this->rolesRepository->findOneByTitle($role->getTitle())) {
          return $this->helperService->generateResponse(409, NULL, array('message' => 'a role with that title already exists'));
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
   *
   * @TODO: Implement this!
   */
  public function updateGroup($id, Group $updatedGroup) {
    throw new HttpException(500, 'Method not implemented.');

    /*
    $role = $this->rolesRepository->findOneById($id);

    if (!$role) {
      return $this->helperService->generateResponse(404, NULL, array('message' => 'role not found'));
    }

    $validation = $this->helperService->validateRole($updatedRole);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], NULL, $validation['errors']);
    }

    if ($role->getId() !== $updatedRole->getId()) {
      return $this->helperService->generateResponse(400, NULL, array('message' => 'ids do not match'));
    }

    // Update db.
    $this->em->merge($updatedRole);
    $this->em->flush();

    return $this->helperService->generateResponse(204);
    */
  }
}
