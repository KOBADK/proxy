<?php
/**
 * @file
 * This holds the BookingService that manages bookings.
 */

namespace Koba\MainBundle\Services;

use Itk\ApiBundle\Services\ExchangeService;
use Koba\MainBundle\Entity\Booking;
use Koba\MainBundle\EntityRepositories\BookingRepository;
use Koba\MainBundle\EntityRepositories\ResourceRepository;
use Koba\MainBundle\EntityRepositories\UserRepository;

/**
 * Class BookingService.
 *
 * @package Koba\MainBundle\Services
 */
class BookingService {
  protected $bookingRepository;
  protected $userRepository;
  protected $resourceRepository;
  protected $exchangeService;

  /**
   * Constructor.
   *
   * @param BookingRepository $bookingRepository
   *   The booking repository.
   * @param UserRepository $userRepository
   *   The user repository.
   * @param ResourceRepository $resourceRepository
   *   The resource repository.
   * @param ExchangeService $exchangeService
   *   The exchange Service
   */
  function __construct(BookingRepository $bookingRepository, UserRepository $userRepository, ResourceRepository $resourceRepository, ExchangeService $exchangeService) {
    $this->bookingRepository = $bookingRepository;
    $this->userRepository = $userRepository;
    $this->resourceRepository = $resourceRepository;
    $this->exchangeService = $exchangeService;
  }

  /**
   * Get all bookings.
   *
   * @return array
   *   Array of Bookings
   */
  public function getAllBookings() {
    $bookings = $this->bookingRepository->findAll();

    return $bookings;
  }

  /**
   * Create a booking for a user.
   *
   * @TODO: Implement this!
   *
   * @param Booking $booking booking
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
   */
  public function createBooking($booking) {
/*    $validation = $this->helperService->validateBooking($booking);
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

    return $this->exchangeService->sendBookingRequest($booking);*/
  }
}
