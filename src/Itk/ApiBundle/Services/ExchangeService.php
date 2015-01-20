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
 * Class ExchangeService
 *
 * @package Itk\ApiBundle\Services
 */
class ExchangeService {
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
    return $this->helperService->generateResponse(500, null, array('message' => 'not implemented'));
  }

  /**
   * Send a booking request to the resource.
   *
   * Creates a vCard of the event to send to the resource.
   *
   * iCalendar doc (p. 52 icalbody):
   * https://www.ietf.org/rfc/rfc2445.txt
   *
   * @param Booking $booking The booking to attempt to make
   * @return array
   */
  public function sendBookingRequestEmail(Booking $booking) {
    $booking->setCompleted(false);
    $booking->setStatusMessage('Mailing request');

    $timestamp = gmdate('Ymd\THis+01');
    $uid  = $timestamp . "-" . $booking->getUser()->getMail();
    $prodId = "-//KOBA//NONSGML v1.0//EN";

    // vCard.
    $message =
      "BEGIN:VCALENDAR\r\n
      VERSION:2.0\r\n
      PRODID:" . $prodId ."\r\n
      METHOD:REQUEST\r\n
      BEGIN:VEVENT\r\n
      UID:" . $uid . "\r\n
      DTSTAMP:" . $timestamp . "\r\n
      DTSTART:" . $booking->getStartDatetimeForVCard() . "\r\n
      DTEND:" . $booking->getEndDatetimeForVCard() . "r\n
      SUMMARY:" . $booking->getSubject() . "\r\n
      ORGANIZER;CN=" . $booking->getUser()->getName() . ":mailto:" . $booking->getUser()->getMail() . "\r\n
      DESCRIPTION:" . $booking->getDescription() . "\r\n
      END:VEVENT\r\n
      END:VCALENDAR\r\n";

    // Send the e-mail.
    $success = mail($booking->getResources(), $subject, $message, $this->ewsHeaders, "-f " . $fromMail);

    if (!$success) {
      return $this->helperService->generateResponse(503, null, array('message' => 'Booking was not delivered to Exchange, try again'));
    }


  }

  public function sendBookingTest() {



    // What resource?
    $resource = 'test@test.test';

    // From who?
    $from = 'test2@test.test';
    $organizer = 'Test Testesen';

    // Date
    $startDateTime = '20141231T080000+01';
    $endDateTime = '20141231T090000+01';

    // Subject
    $subject = 'Dette er et emne';

    // Description
    $description = 'Dette er en beskrivelse.';

    return $this->sendBookingRequestEmail($resource, $from, $organizer, $startDateTime, $endDateTime, $subject, $description);
  }
}