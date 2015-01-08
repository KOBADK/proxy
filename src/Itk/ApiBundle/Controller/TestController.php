<?php

namespace Itk\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Itk\ApiBundle\Entity\User;

/**
 * @Route("/test")
 */
class TestController extends FOSRestController {
  /**
   * @Route("/user")
   * @Method("GET")
   *
   * @ApiDoc(
   *  description="Test function"
   * )
   */
  public function userAction() {
    $user = new User();
    $user->setUuid("1234");
    $user->setName("AJFH");
    $user->setMail("test@test.tt");
    $user->setStatus("active");

    $view = $this->view($user, 200)
      ->setTemplateVar('data');

    return $this->handleView($view);
  }
}