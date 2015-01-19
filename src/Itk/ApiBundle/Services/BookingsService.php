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
    $this->exchangeService->sendBookingTest();

    return $this->helperService->generateResponse(500, array('message' => 'not implemented'));
  }
}