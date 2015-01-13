<?php

namespace Itk\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Itk\ApiBundle\Entity\User;

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

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * @Get("")
   *
   * @ApiDoc(
   *  description="Get all users",
   *  statusCodes={
   *    200="Returned when successful",
   *    404="Returned when no users are found",
   *  }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getUsersAction() {
    $usersService = $this->get('koba.users_service');

    $result = $usersService->getAllUsers();

    $view = $this->view($result['data'], $result['status']);
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
    if ($updatedUser instanceof User === false) {
      return View::create(array('errors' => $updatedUser), 400);
    }

    // Update user
    $result = $usersService->updateUser($id, $updatedUser);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }
}