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
use JMS\Serializer\SerializationContext;

/**
 * @Route("/resources")
 */
class ResourcesController extends FOSRestController {
  /**
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
   */
  public function getResources() {
    $resourcesService = $this->get('koba.resources_service');

    $result = $resourcesService->getAllResources();

    $context = new SerializationContext();
    $context->setGroups(array('resource'));
    $view = $this->view($result['data'], $result['status']);
    $view->setSerializationContext($context);
    return $this->handleView($view);
  }

  /**
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
   * @return \Symfony\Component\HttpFoundation\Response
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

  /**
   * @Post("")
   *
   * @ApiDoc(
   *   description="Create a resource",
   *   input={
   *     "class"="\Itk\ApiBundle\Entity\Resource"
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
   * @return View|\Symfony\Component\HttpFoundation\Response
   */
  public function postResource(Request $request) {
    $resourcesService = $this->get('koba.resources_service');
    $serializer = $this->get('jms_serializer');

    $resource = $serializer->deserialize($request->getContent(), 'Itk\ApiBundle\Entity\Resource', 'json');

    // Add role to user
    $result = $resourcesService->createResource($resource);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }
}