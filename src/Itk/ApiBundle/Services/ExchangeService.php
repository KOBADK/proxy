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
   * @param string $resourceMail The email of the resource to book
   * @param string $fromMail The email of the booker.
   * @param string $fromName Name of the booker.
   * @param string $startDateTime Start time
   *  Complete date plus hours, minutes and seconds: YYYYMMDDThhmmssTZD (eg 19970716T192030+0100)
   *  http://www.w3.org/TR/NOTE-datetime
   * @param string $endDateTime End time
   * @param string $subject Subject
   * @param string $description Description
   * @return array
   */
  public function sendBookingRequestEmail($resourceMail, $fromMail, $fromName, $startDateTime, $endDateTime, $subject, $description) {
    $timestamp = gmdate('Ymd\THis+01');
    $uid  = $timestamp . "|" . $fromMail;
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
      DTSTART:" . $startDateTime . "\r\n
      DTEND:" . $endDateTime . "r\n
      SUMMARY:" . $subject . "\r\n
      ORGANIZER;CN=" . $fromName . ":mailto:" . $fromMail . "\r\n
      DESCRIPTION:" . $description . "\r\n
      END:VEVENT\r\n
      END:VCALENDAR\r\n";

    // Send the e-mail.
    //$success = mail($resourceMail, $subject, $message, $this->ewsHeaders, "-f " . $fromMail);
    $this->helperService->generateResponse(204, array('message' => 'booking request sent'));

    // TODO: mark booking request as sent.
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