<?php
/**
 * @file
 * Contains Koba group service.
 */

namespace Koba\MainBundle\Services;

use Koba\MainBundle\Entity\GroupRepository;
use Koba\MainBundle\Entity\Group;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GroupService
 *
 * @package Koba\MainBundle\Services
 */
class GroupService {
  protected $groupRepository;

  /**
   * Constructor.
   *
   * @param GroupRepository $groupRepository
   *   The group repository.
   */
  public function __construct(GroupRepository $groupRepository) {
    $this->groupRepository = $groupRepository;
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
  }

  /**
   * Update a group.
   *
   * @param integer $id
   *   Group id.
   * @param Group $group
   *   The updated group.
   *
   * @return boolean
   *   Success?
   *
   * @TODO: Implement this!
   */
  public function updateGroup($id, Group $group) {
    throw new HttpException(500, 'Method not implemented.');
  }
}
