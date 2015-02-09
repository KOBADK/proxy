<?php
/**
 * @file
 * Contains the booking controller for /api.
 */

namespace Koba\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
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
   * Get all bookings for current user.
   *
   * @Get("")
   *
   * @ApiDoc(
   *  description="Get all bookings for currently logged in user",
   *  statusCodes={
   *    200="Success"
   *  }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   *
   * @TODO: Implement this!
   */
  public function getBookings() {
    $bookingsService = $this->get('koba.bookings_service');

    $result = $bookingsService->getAllBookingsForCurrentUser();

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * Get booking by id for current user.
   *
   * @Get("/{id}")
   *
   * @ApiDoc(
   *  description="Get booking by id for currently logged in user",
   *  statusCodes={
   *    200="Success"
   *  }
   * )
   *
   * @param integer $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   *
   * @TODO: Implement this!
   */
  public function getBooking($id) {
    $view = $this->view('not implemented', 500);
    return $this->handleView($view);
  }

  /**
   * Create a new booking for current user.
   *
   * @Post("")
   *
   * @ApiDoc(
   *   description="Create a booking for a user",
   *   input={
   *     "class"="\Koba\MainBundle\Entity\Booking"
   *   },
   *   statusCodes={
   *     200="Success (No content)",
   *     400="Validation errors"
   *   },
   *   tags={
   *     "not_implemented",
   *     "no_tests"
   *   }
   * )
   *
   * @param Request $request
   *   Request object.
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   *
   * @TODO: Implement this!
   */
  public function postUserBooking(Request $request) {
    $bookingsService = $this->get('koba.bookings_service');

    $serializer = $this->get('jms_serializer');

    // Deserialize input
    try {
      $booking = $serializer->deserialize($request->getContent(), 'Koba\MainBundle\Entity\Booking', $request->get('_format'));
    } catch (\Exception $e) {
      $view = $this->view(array('message' => 'invalid input'), 400);
      return $this->handleView($view);
    }

    // TODO: Fix this!
    $booking->setStartDateTimeFromUnixTimestamp($booking->getStartDateTime());
    $booking->setEndDateTimeFromUnixTimestamp($booking->getEndDateTime());

    $result = $bookingsService->createBookingForCurrentUser($booking);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * Delete a booking for current user.
   *
   * @Delete("/{id}")
   *
   * @ApiDoc(
   *   description="Delete a booking for current user",
   *   statusCodes={
   *     204="Success (No content)"
   *   },
   *   tags={
   *     "not_implemented",
   *     "no_tests"
   *   }
   * )
   *
   * @param integer $id
   *   The id of the booking to delete.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   *
   * @TODO: Implement this!
   */
  public function deleteUserBooking($id) {
    $view = $this->view('not implemented', 500);
    return $this->handleView($view);
  }

  /**
   * Update a booking for current user.
   *
   * @Put("/{id}")
   *
   * @ApiDoc(
   *   description="Update a booking for current user",
   *   statusCodes={
   *     204="Success (No content)"
   *   },
   *   tags={
   *     "not_implemented",
   *     "no_tests"
   *   }
   * )
   *
   * @param Request $request
   * @param $id
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   *
   * @TODO: Implement this!
   */
  public function putUserBooking(Request $request, $id) {
    $view = $this->view('not implemented', 500);
    return $this->handleView($view);
  }
}
