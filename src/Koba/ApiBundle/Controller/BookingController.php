<?php
/**
 * @file
 * Contains booking controller for ApiBundle.
 */

namespace Koba\ApiBundle\Controller;

use Koba\MainBundle\Exceptions\NotImplementedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * @Route("/bookings")
 */
class BookingController extends FOSRestController {
  /**
   * Get all booking.
   *
   * @FOSRest\Get("")
   *
   * @param Request $request
   *   The request object.
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function getBookings(Request $request) {
    // @TODO: Implement this!
    throw new NotImplementedException();
  }

  /**
   * Post a booking.
   *
   * @FOSRest\Post("")
   *
   * @param Request $request
   *   The request object.
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function postBooking(Request $request) {
    // @TODO: Implement this!
    throw new NotImplementedException();
    // @TODO: Add BOOKING to JSM queue with callback to reservation status...
  }
}

