<?php

namespace Itk\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/test")
 */
class TestController extends FOSRestController {
  /**
   * @Route("")
   * @Method("GET")
   *
   * @ApiDoc(
   *  description="Test function"
   * )
   */
  public function testAction() {
    $data = array("fisk" => "faks");
    $view = $this->view($data, 200)
      ->setTemplateVar('data');

    return $this->handleView($view);
  }
}