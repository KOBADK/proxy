<?php
/**
 * @file
 * Contains index controller for MainBundle.
 */

namespace Koba\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("")
 */
class IndexController extends Controller {
  /**
   * indexAction.
   *
   * @Route("")
   * @Template()
   */
  public function indexAction() {
    return array();
  }
}
