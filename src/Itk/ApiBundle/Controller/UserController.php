<?php

namespace Itk\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Itk\ApiBundle\Entity\User;

/**
 * @Route("/users")
 */
class UserController extends FOSRestController {
  /**
   * @Route("/{id}")
   * @Method("GET")
   *
   * @ApiDoc(
   *  description="Get a user by id",
   *  requirements={
   *    { "name"="id", "dataType"="integer", "requirement"="\d+", "description"="the id of the user" }
   *  },
   *  statusCodes={
   *    200="Returned when successful",
   *    404="Returned when no users are found"
   *  }
   * )
   *
   * @param integer $id
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getUserAction($id) {
    $usersService = $this->get('koba.users_service');

    $result = $usersService->getUser($id);

    $view = $this->view($result['data'], $result['status'])
      ->setTemplateVar('data');

    return $this->handleView($view);
  }

  /**
   * @Route("")
   * @Method("GET")
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

    $view = $this->view($result['data'], $result['status'])
      ->setTemplateVar('data');

    return $this->handleView($view);
  }
}