<?php
/**
 * @file
 * Contains index controller for MainBundle.
 */

namespace Itk\ExchangeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("")
 */
class IndexController extends Controller {
  /**
   * indexAction.
   *
   * @Route("")
   */
  public function indexAction() {
    return new JsonResponse(array('stest' => 'rewt'));
  }
}
