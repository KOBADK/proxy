<?php
/**
 * @file
 * Contains ResourceController.
 */

namespace Koba\AdminBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * @Route("/resources")
 */
class ResourceController extends FOSRestController {
  /**
   * Get resources.
   *
   * @FOSRest\Get("")
   *
   * @return array
   */
  public function getResources() {
    return $this->get('itk.exchange_service')->getResources();
  }
}
