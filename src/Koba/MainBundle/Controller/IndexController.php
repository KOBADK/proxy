<?php
/**
 * @file
 * Contains index controller for MainBundle.
 */

namespace Koba\MainBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * @Route("")
 */
class IndexController extends FOSRestController {
  /**
   * Get index page.
   *
   * @FOSRest\Get("")
   * @Template()
   */
  public function indexAction() {
    return array();
  }
}
