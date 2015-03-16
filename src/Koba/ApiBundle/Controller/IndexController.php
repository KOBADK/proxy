<?php
/**
 * @file
 * Contains index controller for MainBundle.
 */

namespace Koba\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\FOSRestController;

/**
 * @Route("")
 */
class IndexController extends FOSRestController {
  /**
   * IndexAction.
   *
   * @Route("")

   * @param Request $request
   *   The request object.
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function indexAction(Request $request) {
    // Confirm the apikey is accepted.
    $this->get('koba.apikey_service')->getApiKey($request);

    $view = $this->view(array(), 200);
    return $this->handleView($view);
  }
}
