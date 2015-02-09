<?php
/**
 * @file
 * Contains bookings controller for /admin.
 */

namespace Koba\MainBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
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
   * Get all bookings
   *
   * @Get("")
   *
   * @ApiDoc(
   *   description="Get all bookings",
   *   statusCodes={
   *     200="Success"
   *   },
   *   tags={
   *     "no_tests"
   *   }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function getBookings() {
    $bookingsService = $this->get('koba.bookings_service');

    $result = $bookingsService->getAllBookings();

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * Get booking by id
   *
   * @Get("/{id}")
   *
   * @ApiDoc(
   *   description="Get booking by id",
   *   statusCodes={
   *     200="Success",
   *     404="Booking not found"
   *   },
   *   tags={
   *     "no_tests"
   *   }
   * )
   *
   * @param integer $id
   *   Id of the booking.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function getBooking($id) {
    $bookingsService = $this->get('koba.bookings_service');

    $result = $bookingsService->getBooking($id);

    $view = $this->view($result['data'], $result['status']);
    return $this->handleView($view);
  }

  /**
   * Delete a booking
   *
   * @Delete('/{id}')
   *
   * @ApiDoc(
   *   description="Get booking by id",
   *   statusCodes={
   *     204="Success"
   *   },
   *   tags={
   *     "no_tests",
   *     "not_implemented"
   *   }
   * )
   *
   * @param integer $id
   *   Id of the booking to delete.
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   */
  public function deleteBooking($id) {
    $view = $this->view('not implemented', 500);
    return $this->handleView($view);
  }
}
