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
use JMS\JobQueueBundle\Entity\Job;

use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

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
   *         "client_booking_id": -,
   *         "group_id"; -,
   *         "apikey": -
   *       }
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function postBooking(Request $request) {
    $body = $request->getContent();

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

    // Check Access.
    // @TODO: Split into two functions. chechAccess & getConfiguration()
    $apiKeyService->getResourceConfiguration($apiKey, $bodyObj->group_id, $resource->getMail());

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
    $booking->setClientBookingId($bodyObj->client_booking_id);
    $booking->setStatusPending();

    $em = $this->container->get('doctrine')->getManager();

    $em->persist($booking);
    $em->flush();

    // Create job queue items.
    // 1. send booking
    // 2. confirm booking
    // 3. send reply to callback
    $sendJob = new Job('koba:booking:send', array('id' => $booking->getId()));
    $sendJob->addRelatedEntity($booking);
    $sendJob->setRetryStrategy('JMS\\JobQueueBundle\\Entity\\Retry\\FixedIntervalStrategy');
    $sendJob->setRetryStrategyConfig(array('interval' => '+15 seconds'));
    $sendJob->setMaxRetries(5);

    $confirmJob = new Job('koba:booking:confirm', array('id' => $booking->getId()));
    $confirmJob->addRelatedEntity($booking);
    $confirmJob->setRetryStrategy('JMS\\JobQueueBundle\\Entity\\Retry\\FixedIntervalStrategy');
    $confirmJob->setRetryStrategyConfig(array('interval' => '+15 seconds'));
    $confirmJob->setMaxRetries(5);

    $callbackJob = new Job('koba:booking:callback', array('id' => $booking->getId()));
    $callbackJob->addRelatedEntity($booking);
    $callbackJob->setRetryStrategy('JMS\\JobQueueBundle\\Entity\\Retry\\FixedIntervalStrategy');
    $callbackJob->setRetryStrategyConfig(array('interval' => '+15 seconds'));
    $callbackJob->setMaxRetries(5);

    $confirmJob->addDependency($sendJob);
    $callbackJob->addDependency($confirmJob);

    $em->persist($sendJob);
    $em->persist($confirmJob);
    $em->persist($callbackJob);

    $em->flush();
  }


  /**
   * Delete a booking.
   *
   * @FOSRest\Delete("/{clientBookingId}")
   */
  public function deleteBooking(Request $request, $clientBookingId) {
    $apiKeyService = $this->get('koba.apikey_service');

    // Confirm the apikey is accepted.
    $apiKey = $apiKeyService->getApiKey($request->query->apikey);

    // Get the resource. We get it here to avoid more injections in the service.
    $booking = $this->get('doctrine')->getRepository('ItkExchangeBundle:Booking')->findOneByClient($clientBookingId);

    if (!isset($booking)) {
      throw new NotFoundHttpException('booking not found');
    }

    $container = $this->getContainer();
    $doctrine = $container->get('doctrine');
    $em = $doctrine->getManager();

    // Create job queue items.
    // 1. delete booking
    // 2. confirm delete @TODO: Implement this!
    $deleteJob = new Job('koba:booking:delete', array('id' => $booking->getId()));
    $deleteJob->addRelatedEntity($booking);
    $deleteJob->setMaxRetries(5);

    $em->persist($deleteJob);

    $em->flush();
  }
}
