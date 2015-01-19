<?php

namespace Itk\ApiBundle\Controller;

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

use Itk\ApiBundle\Entity\Role;
use JMS\Serializer\SerializationContext;

/**
 * @Route("/roles")
 */
class RolesController extends FOSRestController {
  /**
   * @Get("/{id}")
   *
   * @ApiDoc(
   *   description="Get a role by id",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   statusCodes={
   *     200="Returned when successful",
   *     404="Returned when no roles are found"
   *   }
   * )
   *
   * @param integer $id the id of the role
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getRoleAction($id) {
    $rolesService = $this->get('koba.roles_service');

    $result = $rolesService->getRole($id);

    $context = new SerializationContext();
    $context->setGroups(array('role'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * @Get("")
   *
   * @ApiDoc(
   *   description="Get all roles",
   *   statusCodes={
   *     200="Returned when successful",
   *     404="Returned when no roles are found",
   *   }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getRolesAction() {
    $rolesService = $this->get('koba.roles_service');

    $result = $rolesService->getAllRoles();

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * @Post("")
   *
   * @ApiDoc(
   *   description="Create a role",
   *   input={
   *     "class"="Itk\ApiBundle\Entity\Role",
   *     "groups"={"role_create"}
   *   },
   *   statusCodes={
   *     204="Success",
   *     400="Validation error",
   *     409="A role with that name already exists"
   *   }
   * )
   *
   * @param Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function postRoleAction(Request $request) {
    $rolesService = $this->get('koba.roles_service');
    $serializer = $this->get('jms_serializer');

    // Deserialize role
    $newRole = $serializer->deserialize($request->getContent(), 'Itk\ApiBundle\Entity\Role', $request->get('_format'));

    // Create role
    $result = $rolesService->createRole($newRole);

    // Return response.
    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * @Put("/{id}")
   *
   * @ApiDoc(
   *   description="Update a role",
   *   input={
   *     "class"="Itk\ApiBundle\Entity\Role",
   *     "groups"={"role_update"}
   *   },
   *   statusCodes={
   *     204="Success",
   *     400="Validation error",
   *     404="Role not found"
   *   }
   * )
   *
   * @param integer $id id of the role
   * @param Request $request
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function putRoleAction($id, Request $request) {
    $rolesService = $this->get('koba.roles_service');
    $serializer = $this->get('jms_serializer');

    // Deserialize role
    $newRole = $serializer->deserialize($request->getContent(), 'Itk\ApiBundle\Entity\Role', $request->get('_format'));

    // Update user
    $result = $rolesService->updateRole($id, $newRole);

    // Return response.
    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }
}