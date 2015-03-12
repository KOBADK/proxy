<?php
/**
 * @file
 * Library to handle communication with the Exchange server through ICal
 * formatted mails.
 *
 * This class is used to make changes at the Exchange server as policies don't
 * allow us to preform write operations via the Exchange Web Service (EWS).
 */

namespace Itk\ExchangeBundle\Services;

use Itk\ExchangeBundle\Entity\Booking;
use Itk\ExchangeBundle\Exceptions\ExchangeNotSupported;

/**
 * Class ExchangeBookingService
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeMailService {

  private $mailer;
  private $ics;

  /**
   * Constructor
   *
   * @param $mailer
   *   The mailer used to send mail to Exchange.
   */
  public function __construct($mailer, $ics) {
    $this->mailer = $mailer;
    $this->ics = $ics;

    $this->createBooking();
  }

  /**
   * @param \Itk\ExchangeBundle\Entity\Booking $booking
   *
   * @return mixed
   */
  public function createBooking(Booking $booking) {
    // Get a new ICal calender object.
    $calendar = $this->createCalendar('REQUEST');

    // Create new event in the calender.
    $event = $calendar->newEvent();


    $datetime = new \Datetime('now');

    $event->setStartDate($datetime)
      ->setEndDate($datetime->modify('+5 hours'))
      ->setName('Event 1')
      ->setDescription('Desc for event')
      ->getEvent()->setProperty("organizer", "ical@domain.com",
        array("CN" => "John Doe"));

    return $event->getProperty('UID');
  }

  public function cancelBooking() {
    throw new ExchangeNotSupported();
  }

  public function editBooking() {
    throw new ExchangeNotSupported();
  }

  /**
   * Creates a new iCalcreator calendar object.
   *
   * @param string $method
   *   The method to set in the calender (REQUEST, CANCELLED).
   *
   * @return mixed
   *   The calender object.
   */
  private function createCalendar($method) {
    // Create timezone.
    $tz = $this->ics->createTimezone();
    $tz->setTzid('Europe/Copenhagen')
      ->setProperty('X-LIC-LOCATION', $tz->getTzid());

    // Create calender.
    $calendar = $this->ics->createCalendar($tz);

    // Set request method.
    $calendar->setMethod(strtoupper($method));

    return $calendar;
  }


  private function sendMail($body, $method = 'REQUEST') {


    $headers = "Content-Type:text/calendar; charset=utf-8; method=" . $method . "\r\n";
    $headers .= 'Content-Type: text/plain; charset="utf-8";' . "\r\n";

     $message = $this->mailer->createMessage()
      ->setSubject('You have Completed Registration!')
      ->setFrom('send@example.com')
      ->setTo('recipient@example.com')
      ->setBody($body, 'text/plain');
    $this->mailer->send($message);
  }



  public function sendBookingRequest($booking) {
    $timestamp = gmdate('Ymd\THis+01');
    $uid = $timestamp . '-' . $booking->getUser()->getMail();
    $prodId = '-//KOBA//NONSGML v1.0//EN';

    // vCard.
    $message =
      "BEGIN:VCALENDAR\r\n
      VERSION:2.0\r\n
      PRODID:" . $prodId ."\r\n
      METHOD:REQUEST\r\n
      BEGIN:VEVENT\r\n
      UID:" . $uid . "\r\n
      DTSTAMP:" . $timestamp . "\r\n
      DTSTART:" . $booking->getStartDatetimeForVcard() . "\r\n
      DTEND:" . $booking->getEndDatetimeForVcard() . "r\n
      SUMMARY:" . $booking->getSubject() . "\r\n
      ORGANIZER;CN=" . $this->bookingUser . ':mailto:' . $this->bookingMail . "\r\n
      DESCRIPTION:" . $booking->getDescription() . "\r\n
      END:VEVENT\r\n
      END:VCALENDAR\r\n";

    // Send the e-mail.
    $success = mail(
      $booking->getResource()->getMail(),
      $booking->getSubject(),
      $message,
      self::EWS_HEADERS,
      '-f ' . $booking->getUser()->getMail()
    );
    return $success;
  }

}
