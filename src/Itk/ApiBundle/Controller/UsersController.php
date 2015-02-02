<?php
/**
 * @file
 * @TODO: Missing file description?
 */

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
use Itk\ApiBundle\Entity\Booking;
use Doctrine\DBAL\Types\BooleanType;

/**
 * @Route("/users")
 */
class UsersController extends FOSRestController {
  /**
   * @TODO Missing function description + @see api documentation?
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
   *   @TODO Missing description?
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   @TODO Missing description?
   */
  public function getUser($id) {
    $usersService = $this->get('koba.users_service');

    $result = $usersService->getUser($id);

    $context = new SerializationContext();
    $context->setGroups(array('user'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * @TODO Missing function description + @see api documentation?
   *
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
   *   @TODO Missing description?
   */
  public function getUsers() {
    $usersService = $this->get('koba.users_service');

    $result = $usersService->getAllUsers();

    $context = new SerializationContext();
    $context->setGroups(array('user'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
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
   *     404="user not found"
   *   }
   * )
   *
   * @param Request $request
   *   @TODO Missing description?
   * @param integer $id id of the user
   *   @TODO Missing description?
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   @TODO Missing description?
   */
  public function putUserStatus($id, Request $request) {
    $usersService = $this->get('koba.users_service');
    $serializer = $this->get('jms_serializer');

    // Deserialize input
    try {
      $status = $serializer->deserialize($request->getContent(), 'array', $request->get('_format'));
    } catch (\Exception $e) {
      $view = $this->view(array('message' => 'invalid input'), 400);
      return $this->handleView($view);
    }

    // Update user
    $result = $usersService->setUserStatus($id, $status['status']);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * @TODO Missing function description + @see api documentation?   * @Get("/{id}/roles")
   *
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
   * @param integer $id id of the user
   *   @TODO Missing description?
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   @TODO Missing description?
   */
  public function getUserRoles($id) {
    $usersService = $this->get('koba.users_service');

    $result = $usersService->getUserRoles($id);

    $context = new SerializationContext();
    $context->setGroups(array('user'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * @TODO Missing function description + @see api documentation?
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
   * @param integer $id id of the user
   *   @TODO Missing description?
   * @param Request $request
   *   @TODO Missing description?
   *
   * @return View|\Symfony\Component\HttpFoundation\Response
   *   @TODO Missing description?
   */
  public function postUserRole($id, Request $request) {
    $usersService = $this->get('koba.users_service');
    $serializer = $this->get('jms_serializer');

    // Deserialize input
    try {
      $role = $serializer->deserialize($request->getContent(), 'Itk\ApiBundle\Entity\Role', $request->get('_format'));
    } catch (\Exception $e) {
      $view = $this->view(array('message' => 'invalid input'), 400);
      return $this->handleView($view);
    }

    // Add role to user
    $result = $usersService->addRoleToUser($id, $role);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * @TODO Missing function description + @see api documentation?
   *
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
   *   @TODO Missing description?
   * @param integer $rid role id
   *   @TODO Missing description?
   *
   * @return View|\Symfony\Component\HttpFoundation\Response
   *   @TODO Missing description?
   */
  public function deleteUserRole($id, $rid) {
    $usersService = $this->get('koba.users_service');

    // Remove role from user.
    $result = $usersService->removeRoleFromUser($id, $rid);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * @TODO Missing function description + @see api documentation?
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
   *    404="No users are found"
   *  }
   * )
   *
   * @param integer $id the id of the user
   *   @TODO Missing description?
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   @TODO Missing description?
   */
  public function getUserBookings($id) {
    $usersService = $this->get('koba.users_service');

    $result = $usersService->getUserBookings($id);

    $context = new SerializationContext();
    $context->setGroups(array('booking'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }
}
