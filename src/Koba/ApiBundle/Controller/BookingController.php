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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Itk\ExchangeBundle\Entity\Booking;

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
   *
   *   The body should consist of
   *       {
   *         "subject": -,
   *         "description": -,
   *         "name": -,
   *         "mail": -,
   *         "phone": -,
   *         "start_time": -,
   *         "end_time: -,
   *         "resource": -,
   *         "group_id"; -,
   *         "apikey": -
   *       }
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function postBooking(Request $request) {
    $body = $request->getContent();

    print_r($body);

    if (!isset($body)) {
      throw new NotFoundHttpException('resource not set');
    }
    $bodyObj = json_decode($body);

    $apiKeyService = $this->get('koba.apikey_service');

    // Confirm the apikey is accepted.
    $apiKey = $apiKeyService->getApiKey($bodyObj->apikey);

    // Get the resource. We get it here to avoid more injections in the service.
    $resource = $this->get('doctrine')->getRepository('ItkExchangeBundle:Resource')->findOneByMail($bodyObj->resource);

    if (!isset($resource)) {
      throw new NotFoundHttpException('resource not found');
    }

    // Get resource configuration and check Access.
    $apiKeyConfiguration = $apiKeyService->getResourceConfiguration($apiKey, $bodyObj->group_id, $resource->getMail());

    // Create a test booking.
    $booking = new Booking();
    $booking->setSubject($bodyObj->subject);
    $booking->setDescription($bodyObj->description);
    $booking->setName($bodyObj->name);
    $booking->setMail($bodyObj->mail);
    $booking->setPhone($bodyObj->phone);
    $booking->setStartTime($bodyObj->start_time);
    $booking->setEndTime($bodyObj->end_time);
    $booking->setResource($resource);
    $booking->setApiKey($apiKey->getApiKey());
    $booking->setStatusPending();

    $manager = $this->container->get('doctrine')->getManager();

    $manager->persist($booking);
    $manager->flush();

    // Create job queue items.
    // 1. send booking
    // 2. confirm booking
    // 3. send reply to callback

  }
}
