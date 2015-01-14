<?php

namespace Itk\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/users")
 */
class UsersController extends FOSRestController {
  /**
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
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getUserAction($id) {
    $usersService = $this->get('koba.users_service');

    $result = $usersService->getUser($id);

    $context = new SerializationContext();
    $context->setGroups(array('user'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * @Get("")
   *
   * @ApiDoc(
   *  description="Get all users",
   *  statusCodes={
   *    200="Returned when successful"
   *  }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getUsersAction() {
    $usersService = $this->get('koba.users_service');

    $result = $usersService->getAllUsers();

    $context = new SerializationContext();
    $context->setGroups(array('user'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * @Put("/{id}")
   *
   * @ApiDoc(
   *   description="Update the user",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   input={
   *     "class"="Itk\ApiBundle\Entity\User",
   *     "groups"={"user_update"}
   *   },
   *   statusCodes={
   *     204="Returned when successful",
   *     400="Malformed input",
   *     404="user not found"
   *   }
   * )
   *
   * @param Request $request
   * @param integer $id id of the user
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function putUserAction($id, Request $request) {
    $usersService = $this->get('koba.users_service');
    $serializer = $this->get('jms_serializer');

    // Deserialize user
    $updatedUser = $serializer->deserialize($request->getContent(), 'Itk\ApiBundle\Entity\User', $request->get('_format'));

    // Update user
    $result = $usersService->updateUser($id, $updatedUser);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * @Get("/{id}/roles")
   *
   * @ApiDoc(
   *   description="Get a user's roles",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   statusCodes={
   *     200="Success",
   *     404="User not found"
   *   }
   * )
   *
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getUserRolesAction($id) {
    $usersService = $this->get('koba.users_service');

    $result = $usersService->getUserRoles($id);

    $context = new SerializationContext();
    $context->setGroups(array('user'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
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
   * @param $id
   * @param Request $request
   * @return View|\Symfony\Component\HttpFoundation\Response
   */
  public function postUserRoleAction($id, Request $request) {
    $usersService = $this->get('koba.users_service');
    $serializer = $this->get('jms_serializer');

    $role = $serializer->deserialize($request->getContent(), 'Itk\ApiBundle\Entity\Role', 'json');

    // Add role to user
    $result = $usersService->addRoleToUser($id, $role);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * @Delete("/{id}/roles/{rid}")
   *
   * @ApiDoc(
   *   description="Remove a role from a user",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"},
   *     {"name"="rid", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   statusCodes={
   *     204="Success (No content)",
   *     404={
   *       "User not found",
   *       "Role not found"
   *     },
   *     409="User does not have that role"
   *   }
   * )
   *
   * @param integer $id user id
   * @param integer $rid role id
   * @return View|\Symfony\Component\HttpFoundation\Response
   */
  public function deleteUserRole($id, $rid) {
    $usersService = $this->get('koba.users_service');

    // Remove role from user.
    $result = $usersService->removeRoleFromUser($id, $rid);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * @Get("/{id}/bookings")
   *
   * @ApiDoc(
   *  description="Get a user's bookings",
   *  requirements={
   *    {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *  },
   *  statusCodes={
   *    200="Success",
   *    404="No users are found"
   *  }
   * )
   *
   * @param integer $id the id of the user
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getUserBookings($id) {
    $usersService = $this->get('koba.users_service');

    $result = $usersService->getUserBookings($id);

    $context = new SerializationContext();
    $context->setGroups(array('booking'));
    $view = $this->view($result['data'], $result['status']);
// @TODO:    $view->setSerializationContext($context);
    return $this->handleView($view);
  }
}