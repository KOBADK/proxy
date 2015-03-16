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

    $provider = $this->get('itk.exchange_mail_service');

    return new JsonResponse(array('stest' => 'rewt'));
  }


  /**
   * @Route("/list")
   */
  public function listResources() {

    $ws = $this->get('itk.exchange_web_service');

    $ws->getRessources();

    return new JsonResponse(array('stest' => 'rewt'));
  }
}
