<?php
/**
 * @file
 * Contains the resources controller for /api.
 */

namespace Koba\ApiBundle\Controller;

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
 * @Route("/resources")
 */
class ResourcesController extends FOSRestController {
  /**
   * Get all resources for currently logged in user.
   *
   * @Get("")
   *
   * @ApiDoc(
   *   description="Get all resources",
   *   statusCodes={
   *     200="Success"
   *   },
   *   tags={
   *     "no_tests"
   *   }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   *
   * @TODO: Implement this!
   */
  public function getResources() {
    $resourcesService = $this->get('koba.resources_service');

    $result = $resourcesService->getAllResourcesForUser();

    $context = new SerializationContext();
    $context->setGroups(array('resource'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * Get resource by id.
   *
   * @Get("/{id}")
   *
   * @ApiDoc(
   *   description="Get a resource by id",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   statusCodes={
   *     200="Returned when successful",
   *     404="Returned when no users are found"
   *   },
   *   tags={
   *     "no_tests"
   *   }
   * )
   *
   * @param integer $id the id of the user
   *   Id of user.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   *
   * @TODO: Implement this!
   */
  public function getResource($id) {
    $resourcesService = $this->get('koba.resources_service');

    $result = $resourcesService->getResource($id);

    $context = new SerializationContext();
    $context->setGroups(array('resource'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }
}
