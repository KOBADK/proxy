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
    $apiKey = $this->get('koba.apikey_service')->getApiKey($request);

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
    // Confirm the apikey is accepted.
    $this->get('koba.apikey_service')->getApiKey($request);

    $content = $request->getContent();

    throw new NotImplementedException();

    $view = $this->view($content, 200);
    return $this->handleView($view);
  }

}
