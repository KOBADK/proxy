<?php
/**
 * @file
 * Contains the resources controller for /admin.
 */

namespace Koba\AdminBundle\Controller;

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
 * @Route("/resources")
 */
class ResourcesController extends FOSRestController {
  /**
   * Get all resources.
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
   */
  public function getResources() {
    $resourceService = $this->get('koba.resource_service');

    $result = $resourceService->getAllResources();

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
   *   Id of the resource.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function getResource($id) {
    $resourceService = $this->get('koba.resource_service');

    $result = $resourceService->getResource($id);

    $context = new SerializationContext();
    $context->setGroups(array('resource'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
   * Create a new resource
   *
   * @Post("")
   *
   * @ApiDoc(
   *   description="Create a resource",
   *   input={
   *     "class"="\Koba\MainBundle\Entity\Resource",
   *     "groups"={"resource_create"}
   *   },
   *   statusCodes={
   *     204="Success (No content)",
   *     400="Validation errors",
   *     409="Resource already exists"
   *   },
   *   tags={
   *     "not_implemented"
   *   }
   * )
   *
   * @param Request $request
   *   Request object.
   *
   * @return View|\Symfony\Component\HttpFoundation\Response
   *   Response object.
   *
   * @TODO: Implement this!
   */
  public function postResource(Request $request) {
    $resourceService = $this->get('koba.resource_service');
    $serializer = $this->get('jms_serializer');

    // Deserialize input
    try {
      $resource = $serializer->deserialize($request->getContent(), 'Koba\MainBundle\Entity\Resource', $request->get('_format'));
    } catch (\Exception $e) {
      $view = $this->view(array('message' => 'invalid input'), 400);
      return $this->handleView($view);
    }

    $resourceService->createResource($resource);

    $view = $this->view('not implemented', 500);
    return $this->handleView($view);
  }

  /**
   * Delete a resource.
   *
   * @Delete("")
   *
   * @ApiDoc(
   *   description="Delete a resource",
   *   requirements={
   *     {"name"="id", "dataType"="integer", "requirement"="\d+"}
   *   },
   *   statusCodes={
   *     204="Success (No content)",
   *     404="Resource not found"
   *   },
   *   tags={
   *     "not_implemented",
   *     "no_tests"
   *   }
   * )
   *
   * @param integer $id
   *   Id of the resource to delete.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   *
   * @TODO: Implement this!
   */
  public function deleteResource($id) {
    $resourceService = $this->get('koba.resource_service');

    $resourceService->deleteResource($id);

    $view = $this->view(NULL, 204);
    return $this->handleView($view);
  }
}
