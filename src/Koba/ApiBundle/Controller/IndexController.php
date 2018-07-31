<?php
/**
 * @file
 * Contains index controller for MainBundle.
 */

namespace Koba\ApiBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * @Route("")
 */
class IndexController extends FOSRestController {
  /**
   * Get index.
   *
   * @FOSRest\Get("")

   * @param Request $request
   *   The request object.
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function indexAction(Request $request) {
    // Confirm the apikey is accepted.
    $this->get('koba.apikey_service')->getApiKey($request->query->get('apikey'));

    $view = $this->view(array(), 200);
    return $this->handleView($view);
  }
}
