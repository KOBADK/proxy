<?php

namespace Itk\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/bookings")
 */
class BookingsController extends FOSRestController {
  /**
   * @Get("")
   *
   * @ApiDoc(
   *  description="Get all bookings",
   *  statusCodes={
   *    200="Success"
   *  }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function getBookings() {
    $bookingsService = $this->get('koba.bookings_service');

    $result = $bookingsService->getAllBookings();

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }
}