<?php
/**
 * @file
 * Contains ResourceController.
 */

namespace Koba\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/resources")
 */
class ResourceController extends FOSRestController {
  /**
   * Get resources.
   *
   * @FOSRest\Get("")
   *
   * @param Request $request
   *   The request object.
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function getResources(Request $request) {
    // Confirm the apikey is accepted.
    $apiKey = $this->get('koba.apikey_service')->getApiKey($request);

    $view = $this->view($apiKey, 200);
    return $this->handleView($view);
  }
}
