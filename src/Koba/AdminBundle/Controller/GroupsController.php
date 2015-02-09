<?php
/**
 * @file
 * Contains the groups controller for /admin.
 */

namespace Koba\AdminBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/groups")
 */
class GroupsController extends FOSRestController {
  /**
   * Get all groups.
   *
   * @Get("")
   *
   * @ApiDoc(
   *   description="Get all groups",
   *   statusCodes={
   *     200="Returned when successful"
   *   },
   *   tags={
   *     "not_implemented",
   *     "not_tested"
   *   }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function getGroups() {
    $groupService = $this->get('koba.group_service');

    $result = $groupService->getAllGroups();

    $context = new SerializationContext();
    $context->setGroups(array('group'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * Get group by id.
   *
   * @Get("/{id}")
   *
   * @ApiDoc(
   *   description="Get a group by id",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   statusCodes={
   *     200="Returned when successful",
   *     404="Returned when no group is found"
   *   },
   *   tags={
   *     "not_implemented",
   *     "not_tested"
   *   }
   * )
   *
   * @param integer $id
   *   Id of the group.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function getGroup($id) {
    $groupService = $this->get('koba.group_service');

    $result = $groupService->getGroup($id);

    $context = new SerializationContext();
    $context->setGroups(array('group'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * Create a new group.
   *
   * @Post("")
   *
   * @ApiDoc(
   *   description="Create a group",
   *   input={
   *     "class"="Koba\MainBundle\Entity\Group",
   *     "groups"={"group_create"}
   *   },
   *   statusCodes={
   *     204="Success",
   *     400="Validation error",
   *     409="A group with that name already exists"
   *   },
   *   tags={
   *     "not_implemented",
   *     "not_tested"
   *   }
   * )
   *
   * @param Request $request
   *   Request object.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function postGroup(Request $request) {
    $groupService = $this->get('koba.group_service');
    $serializer = $this->get('jms_serializer');

    // Deserialize input
    try {
      $group = $serializer->deserialize($request->getContent(), 'Koba\MainBundle\Entity\Group', $request->get('_format'));
    } catch (\Exception $e) {
      $view = $this->view(array('message' => 'invalid input'), 400);
      return $this->handleView($view);
    }

    // Create role
    $result = $groupService->createGroup($group);

    // Return response.
    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * Update a group.
   *
   * @Put("/{id}")
   *
   * @ApiDoc(
   *   description="Update a group",
   *   input={
   *     "class"="Koba\MainBundle\Entity\Group",
   *     "groups"={"group_update"}
   *   },
   *   statusCodes={
   *     204="Success",
   *     400="Validation error",
   *     404="Group not found"
   *   },
   *   tags={
   *     "not_implemented",
   *     "not_tested"
   *   }
   * )
   *
   * @param integer $id
   *   Id of the role.
   * @param Request $request
   *   Request object.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function putGroup($id, Request $request) {
    $groupService = $this->get('koba.group_service');
    $serializer = $this->get('jms_serializer');

    // Deserialize input
    try {
      $group = $serializer->deserialize($request->getContent(), 'Koba\MainBundle\Entity\Group', $request->get('_format'));
    } catch (\Exception $e) {
      $view = $this->view(array('message' => 'invalid input'), 400);
      return $this->handleView($view);
    }

    // Update user
    $result = $groupService->updateGroup($id, $group);

    // Return response.
    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * Delete a group with id.
   *
   * @Delete("/{id}")
   *
   * @ApiDoc(
   *   description="Delete a group",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   statusCodes={
   *     204="Success",
   *     400="Validation error",
   *     404="Group not found"
   *   },
   *   tags={
   *     "not_implemented",
   *     "not_tested"
   *   }
   * )
   *
   * @param integer $id
   *   Id of the group to delete.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function deleteGroup($id) {
    $view = $this->view('not implemented', 500);
    return $this->handleView($view);
  }
}
