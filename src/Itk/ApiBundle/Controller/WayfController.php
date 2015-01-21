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


/**
 * @Route("/wayf")
 */
class WayfController extends FOSRestController {
  /**
   * @Get("/login")
   *
   * @ApiDoc(
   *  description="Send a user to WAYF",
   *  statusCodes={
   *    200="Returned when successful",
   *  }
   * )
   *
   */
  public function getLoginAction() {
    $wayfService = $this->get('koba.wayf_service');
    $wayfService->authenticate();
  }

  /**
   * @Post("/login")
   *
   * @ApiDoc(
   *  description="Gets user back from WAYF",
   *  statusCodes={
   *    200="Returned when successful",
   *  }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function PostLoginAction() {
    $wayfService = $this->get('koba.wayf_service');
    $result = $wayfService->authenticate();

    $mail = $result['attributes']['mail'][0];
    $firstName = $result['attributes']['gn'][0];
    $lastName = $result['attributes']['sn'][0];
    $uniqId = $result['attributes']['eduPersonTargetedID'][0];
  }
}
