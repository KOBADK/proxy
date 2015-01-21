<?php
/**
 * @file
 * This file is a part of the Itk ApiBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Itk\ApiBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Itk\ApiBundle\Entity\Booking;

/**
 * Class BookingsService
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
   * Constructor
   *
   * @param Container $container
   * @param HelperService $helperService
   */
  function __construct(Container $container, HelperService $helperService) {
    $this->container = $container;
    $this->helperService = $helperService;
    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();

    $this->bookingRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Booking');
    $this->userRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User');
    $this->resourceRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Resource');

    $this->exchangeService = $this->container->get('koba.exchange_service');
  }

  /**
   * Get all bookings
   *
   * @return array
   */
  public function getAllBookings() {
    $bookings = $this->bookingRepository->findAll();

    return $this->helperService->generateResponse(200, $bookings);
  }


  /**
   * Create a booking for a user
   *
   * @param Booking $booking booking
   * @return array
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