<?php
/**
 * @file
 * Contains the users controller for /admin
 */

namespace Koba\AdminBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use Doctrine\DBAL\Types\BooleanType;

/**
 * @Route("/users")
 */
class UsersController extends FOSRestController {
  /**
   * Get a user by id.
   *
   * @Get("/{id}")
   *
   * @ApiDoc(
   *  description="Get a user by id",
   *  requirements={
   *    {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *  },
   *  statusCodes={
   *    200="Returned when successful",
   *    404="Returned when no users are found"
   *  }
   * )
   *
   * @param integer $id the id of the user
   *   Id of the user.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function getUser($id) {
    $userService = $this->get('koba.user_service');

    $result = $userService->getUser($id);

    $context = new SerializationContext();
    $context->setGroups(array('user'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * Get all users.
   *
   * @Get("")
   *
   * @ApiDoc(
   *  resource = true,
   *  description="Get all users",
   *  statusCodes={
   *    200="Returned when successful"
   *  }
   * )
   *
   * @param Request $request
   *   Request object.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function getUsers(Request $request) {
    $userService = $this->get('koba.user_service');

    $result = $userService->getAllUsers();

    $context = new SerializationContext();
    $context->setGroups(array('user'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * Update a user's status.
   *
   * @Put("/{id}/status")
   *
   * @ApiDoc(
   *   description="Update user status",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   input={
   *     "class"="\Itk\ApiBundle\Entity\User",
   *     "groups"={"userstatus"}
   *   },
   *   statusCodes={
   *     204="Returned when successful",
   *     400="Malformed input",
   *     404="User not found"
   *   }
   * )
   *
   * @param Request $request
   *   Request object.
   * @param integer $id id of the user
   *   Id of the user.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function putUserStatus($id, Request $request) {
    $userService = $this->get('koba.user_service');
    $serializer = $this->get('jms_serializer');

    // Deserialize input
    try {
      $user = $serializer->deserialize($request->getContent(), 'array', $request->get('_format'));
    } catch (\Exception $e) {
      $view = $this->view(array('message' => 'invalid input'), 400);
      return $this->handleView($view);
    }

    // Update user
    $result = $userService->setUserStatus($id, $user['status']);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * Get user groups.
   *
   * @Get("/{id}/group")
   *
   * @ApiDoc(
   *   description="Get a user's groups",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   statusCodes={
   *     200="Success",
   *     404="User not found"
   *   }
   * )
   *
   * @param integer $id
   *   Id of the user
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function getUserGroups($id) {
    $userService = $this->get('koba.user_service');

    $result = $userService->getUserGroups($id);

    $context = new SerializationContext();
    $context->setGroups(array('group'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * Add a group to user.
   *
   * @Post("/{id}/roles")
   *
   * @ApiDoc(
   *   description="Add a role to a user",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   input={
   *     "class"="\Itk\ApiBundle\Entity\Role"
   *   },
   *   statusCodes={
   *     204="Success (No content)",
   *     400="Validation errors",
   *     404={
   *       "User not found",
   *       "Role not found"
   *     },
   *     409="User already has that role"
   *   }
   * )
   *
   * @param integer $id
   *   Id of the user.
   * @param Request $request
   *   Request object.
   *
   * @return View|\Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function postUserGroup($id, Request $request) {
    $userService = $this->get('koba.user_service');
    $serializer = $this->get('jms_serializer');

    // Deserialize input
    try {
      $group = $serializer->deserialize($request->getContent(), 'Koba\MainBundle\Entity\Role', $request->get('_format'));
    } catch (\Exception $e) {
      $view = $this->view(array('message' => 'invalid input'), 400);
      return $this->handleView($view);
    }

    // Add role to user
    $result = $userService->addGroupToUser($id, $group);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * Remove group from user.
   *
   * @Delete("/{id}/roles/{gid}")
   *
   * @ApiDoc(
   *   description="Remove a group from a user",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"},
   *     {"name"="gid", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   statusCodes={
   *     204="Success (No content)",
   *     404={
   *       "User not found",
   *       "Group not found"
   *     },
   *     409="User does not have that group"
   *   }
   * )
   *
   * @param integer $id
   *   Id of the user.
   * @param integer $gid
   *   Id of the group.
   *
   * @return View|\Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function deleteUserRole($id, $gid) {
    $userService = $this->get('koba.user_service');

    // Remove role from user.
    $result = $userService->removeGroupFromUser($id, $gid);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * Get all bookings for user.
   *
   * @Get("/{id}/bookings")
   *
   * @ApiDoc(
   *  description="Get a user's bookings",
   *  requirements={
   *    {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *  },
   *  statusCodes={
   *    200="Success",
   *    404="No user are found"
   *  }
   * )
   *
   * @param integer $id
   *   The id of the user
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function getUserBookings($id) {
    $userService = $this->get('koba.user_service');

    $result = $userService->getUserBookings($id);

    $context = new SerializationContext();
    $context->setGroups(array('booking'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }
}
