<?php
/**
 * @file
 * Contains the exchange mock.
 */
namespace Itk\ExchangeBundle\Mocks;

use Koba\MainBundle\Entity\Booking;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Itk\ExchangeBundle\Services\ExchangeService;

/**
 * Class ExchangeServiceMock
 *
 * @package Itk\ApiBundle\Services
 */
class ExchangeServiceMock extends ExchangeService {
  /**
   * Constructor
   */
  public function __construct() {

  }

  /**
   * Get a resource from exchange
   *
   * @param string $mail
   *   The mail that identifies the resource in Exchange.
   *
   * @return Resource
   *   The resource.
   */
  public function getResource($mail) {
    return array();
  }

  /**
   * Mock of sendBookingRequest.
   *
   * The subject is used to fake different results.
   *
   * @param Booking $booking
   *   The booking to attempt to make
   *
   * @return Booking
   *   The booking.
   */
  public function sendBookingRequest(Booking $booking) {
    if ($booking->getSubject() === 'success') {
      return $booking;
    }
    else if ($booking->getSubject() === 'error_mail_not_received') {
      throw new HttpException(503);
    }
  }
}
