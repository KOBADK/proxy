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

/**
 * @Route("/resources")
 */
class ResourcesController extends FOSRestController {
  /**
   * @Get("")
   *
   * @ApiDoc(
   *  description="Get all resources",
   *  statusCodes={
   *    200="Success"
   *  }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getResources() {
    $resourcesService = $this->get('koba.resources_service');

    $result = $resourcesService->getAllResources();

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }
}