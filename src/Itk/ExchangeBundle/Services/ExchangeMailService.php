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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

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
  }

  /**
   * @param \Itk\ExchangeBundle\Entity\Booking $booking
   *
   * @return string
   *   The unique ID send with the request to Exchange.
   */
  public function createBooking(Booking $booking) {
    // Get a new ICal calender object.
    $calendar = $this->createCalendar('REQUEST');

    // Create new event in the calender.
    $event = $calendar->newEvent();

    // @TODO: Get location.

    // Set event information.
    $event->setStartDate(new \Datetime($booking->getStartTime()))
      ->setEndDate(new \DateTime($booking->getEndTime()))
      ->setName($booking->getSubject())
      ->setDescription($booking->getDescription())
//      ->setLocation($booking->getResource()->location())
      ->getEvent()->setProperty("organizer", $booking->getMail(), array("CN" => $booking->getName()));

    $booking->setExchangeId($event->getProperty('UID'));

    // @TODO: Encode booking information in the vevent description.
//    $encoders = array(new XmlEncoder(), new JsonEncoder());
//    $normalizers = array(new GetSetMethodNormalizer());
//    $normalizers[0]->setIgnoredAttributes(array('resource'));
//    $serializer = new Serializer($normalizers, $encoders);
//    echo $serializer->serialize($booking, 'json');


    // Get the calendar as an formatted string and send mail.
    $this->sendMail('jeskr@aarhus.dk', 'Test booking', $calendar->getCalendar()->createCalendar(), 'REQUEST');

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


  private function sendMail($to, $subject, $body, $method = 'REQUEST') {


     $message = $this->mailer->createMessage()
      ->setSubject($subject)
      ->setFrom('send@example.com')
      ->setTo($to)
      ->setBody($body, 'text/plain');

    $type = $message->getHeaders()->get('Content-Type');
    $type->setValue('text/calendar');
    $type->setParameters(array(
      'charset' => 'utf-8',
      'method' => $method,
    ));

    echo $message;

    $this->mailer->send($message);
  }
}
