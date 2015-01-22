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
use Symfony\Component\Security\Acl\Exception\Exception;

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
   *     200="Success (No content)",
   *     400="Validation errors",
   *     404={
   *       "User not found",
   *       "Resource not found"
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

    $serializer = $this->get('jms_serializer');

    // Deserialize input
    try {
      $booking = $serializer->deserialize($request->getContent(), 'Itk\ApiBundle\Entity\Booking', $request->get('_format'));
    } catch (\Exception $e) {
      $view = $this->view(array('message' => 'invalid input'), 400);
      return $this->handleView($view);
    }

    $booking->setStartDateTimeFromUnixTimestamp($booking->getStartDateTime());
    $booking->setEndDateTimeFromUnixTimestamp($booking->getEndDateTime());

    $result = $bookingsService->createBooking($booking);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }
}