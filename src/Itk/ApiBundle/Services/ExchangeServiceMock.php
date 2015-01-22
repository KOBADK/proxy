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
use Itk\ApiBundle\Entity\Resource;
use Itk\ApiBundle\Entity\User;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class ExchangeServiceMock
 *
 * @package Itk\ApiBundle\Services
 */
class ExchangeServiceMock extends ExchangeService {
  protected $container;
  protected $doctrine;
  protected $em;
  protected $helperService;
  protected $ewsHeaders;

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
    $this->ewsHeaders = "Content-Type:text/calendar; charset=utf-8; method=REQUEST\r\n";
    $this->ewsHeaders .= "Content-Type: text/plain; charset=\"utf-8\" \r\n";
  }

  /**
   * Get a resource from exchange
   *
   * @param string $mail the mail that identifies the resource in Exchange
   * @return array
   */
  public function getResource($mail) {
    if ($mail === 'resource1@test.test') {

    }
  }

  /**
   * Mock of sendBookingRequest
   *
   * subject = 'success'
   * subject = 'error_mail_not_received'
   *
   * @param Booking $booking The booking to attempt to make
   * @return array
   */
  public function sendBookingRequest(Booking $booking) {
    if ($booking->getSubject() === 'success') {
      $booking->setStatusMessage('Mail sent');
      $booking->setCompleted(true);
      $booking->setEid("123123");
      $this->em->flush();
      return $this->helperService->generateResponse(201, $booking);
    }
    else if ($booking->getSubject() === 'error_mail_not_received') {
      $booking->setStatusMessage('Mail not received by resource');
      $this->em->flush();
      return $this->helperService->generateResponse(503, null, array('message' => 'Booking request was not delivered to resource, try again'));
    }
  }
}