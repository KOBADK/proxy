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
use Symfony\Component\HttpFoundation\Session\Session;

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
    // Send the user to WAYF.
    $wayfService = $this->get('koba.wayf_service');
    $wayfService->request();
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
    // Parse and verify post data from WAYF.
    $wayfService = $this->get('koba.wayf_service');
    $result = $wayfService->response();

    // Set needed attributes.
    $mail = $result['attributes']['mail'][0];
    $firstName = $result['attributes']['gn'][0];
    $lastName = $result['attributes']['sn'][0];
    $uniqId = $result['attributes']['eduPersonTargetedID'][0];

    // Save data to user entity.

    // Setup a session for the user.
    $session = new Session();
    $session->start();
    $session->set('uniqId', $uniqId);

    // Return a reply to the end user.
  }

  /**
   * @Get("/token")
   *
   * @ApiDoc(
   *  description="Get logged in token",
   *  statusCodes={
   *    200="Returned when successful",
   *  }
   * )
   *
   */
  public function getTokenAction() {
    // Verify user.
    $session = new Session();
    $uniqId = $session->get('uniqId');
    if ($uniqId) {
      // User is valid, send token!
    }
    else {
      // User is NOT valid. Return a reply.
    }
  }
}
