<?php
/**
 * @file
 * @todo Missing file description?
 */

namespace Itk\ApiBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Itk\ApiBundle\Entity\Booking;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class BookingsService.
 *
 * @package Itk\ApiBundle\Services
 */
class BookingsService {
  protected $container;
  protected $doctrine;
  protected $em;
  protected $bookingRepository;
  protected $helperService;
  protected $exchangeService;

  /**
   * Constructor.
   *
   * @param Container $container
   * @param HelperService $helperService
   */
  function __construct(Container $container, HelperService $helperService) {
    $this->helperService = $helperService;

    // @todo: The service is only dependent on the container to get the entity
    // manager and exchange service?
    $this->container = $container;

    // @TODO: Inject "EntityManager $em" -> "@doctrine.orm.entity_manager" so
    // it's not dependent on doctrine inside the service.
    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();

    $this->bookingRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Booking');
    $this->userRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User');
    $this->resourceRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Resource');

    // @TODO: Inject the service, so it's not hard dependent will make tests
    // possible?
    $this->exchangeService = $this->container->get('koba.exchange_service');
  }

  /**
   * Get all bookings.
   *
   * @return array
   *   @TODO Missing description?
   */
  public function getAllBookings() {
    $bookings = $this->bookingRepository->findAll();

    return $this->helperService->generateResponse(200, $bookings);
  }

  /**
   * Ask EWS for each resources bookings.
   *
   * @return array
   *   @TODO Missing description?
   */
  public function getAllExchangeBookings() {
    // Get resources.
    $resources = $this->resourceRepository->findAll();

    // @TODO: Use mktime and date to format it's a lot faster. If date is  ISO 8601 date
    // then date('c', mktime(0, 0, 0)) would be start of day.
    // Calculate the start & end of the current day.
    $startToday = new \DateTime();
    $startToday->setTime(0, 0, 0);
    $endToday = new \DateTime();
    $endToday->setTime(23, 59, 59);

    // Iterate each resource and find bookings.
    $items = array();
    foreach ($resources as $resource) {
      $items = $this->exchangeService->listAction(
        $resource->getMail(),
        $startToday->format("Ymd\THis\Z"),
        $endToday->format("Ymd\THis\Z"));
    }

    return $this->helperService->generateResponse(200, $items);
  }


  /**
   * Create a booking for a user.
   *
   * @param Booking $booking booking
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
   */
  public function createBooking($booking) {
    $validation = $this->helperService->validateBooking($booking);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    $user = $this->userRepository->findOneById($booking->getUser()->getId());

    if (!$user) {
      return $this->helperService->generateResponse(404, null, array('message' => 'user not found'));
    }

    $booking->setUser($user);

    $resource = $this->resourceRepository->findOneById($booking->getResource()->getId());

    if (!$resource) {
      return $this->helperService->generateResponse(404, null, array('message' => 'resource not found'));
    }

    $booking->setResource($resource);

    $booking->setCompleted(false);
    $booking->setStatusMessage('Sending request');
    $this->em->persist($booking);
    $this->em->flush();

    return $this->exchangeService->sendBookingRequest($booking);
  }
}
