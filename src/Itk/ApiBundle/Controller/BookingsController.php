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


  /**
   * @Post("")
   *
   * @ApiDoc(
   *   description="Create a booking for a user",
   *   input={
   *     "class"="\Itk\ApiBundle\Entity\Booking"
   *   },
   *   statusCodes={
   *     204="Success (No content)",
   *     400="Validation errors",
   *     404={
   *       "User not found"
   *     }
   *   },
   *   tags={
   *     "not_implemented",
   *     "no_tests"
   *   }
   * )
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function postUserBooking(Request $request) {
    $bookingsService = $this->get('koba.bookings_service');


    // TODO: Implement this
    $result = $bookingsService->createBooking(null);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }
}