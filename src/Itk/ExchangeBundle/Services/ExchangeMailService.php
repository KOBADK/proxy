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
use Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException;
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
  /**
   * The service account information to use.
   *
   * @var array
   */
  private $account;

  /**
   * Mail client and ICalender generator.
   */
  private $mailer;
  private $ics;

  /**
   * Constructor
   *
   * @param $username
   *   Service account username.
   * @param $password
   *   Service account password.
   * @param $mail
   *   Service account mail address.
   * @param $mailer
   *   The mailer used to send mail to Exchange.
   * @param $ics
   *   The ICal provider, which is used generate vevent's.
   */
  public function __construct($username, $password, $mail, $mailer, $ics) {
    $this->account = array(
      'username' => $username,
      'password' => $password,
      'mail' => $mail,
    );

    $this->mailer = $mailer;
    $this->ics = $ics;
  }

  /**
   * Create a booking at Exchange.
   *
   * @param \Itk\ExchangeBundle\Entity\Booking $booking
   */
  public function createBooking(Booking $booking) {
    // Get a new ICal calender object.
    $calendar = $this->createCalendar('REQUEST');

    // Create new event in the calender.
    $event = $calendar->newEvent();

    // Encode booking information in the vevent description.
    $description = '<koba><name>' . $booking->getName() . '</name><description>' . $booking->getDescription() . '</description></koba>';

    // Set event information.
    $event->setStartDate(\DateTime::createFromFormat( 'U', $booking->getStartTime()))
      ->setEndDate(\DateTime::createFromFormat( 'U', $booking->getEndTime()))
      ->setName($booking->getSubject())
      ->setDescription($description)
      ->setLocation($booking->getResource()->getName());

    $e = $event->getEvent();
    $e->setOrganizer($booking->getMail(), array('CN' => $booking->getName()));
    $e->setTransp('TRANSPARENT');
    $e->setClass('PUBLIC');

    // Set the newly create exchange ID.
    $booking->setExchangeId($event->getProperty('UID'));

    // Get the calendar as an formatted string and send mail.
    $this->sendMail($booking->getResource()->getMail(), $booking->getSubject(), $calendar->returnCalendar(), 'REQUEST');
  }

  /**
   * Cancel an exiting booking.
   *
   * @throws \Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException
   */
  public function cancelBooking() {
    throw new ExchangeNotSupportedException();
  }

  /**
   * Update or edit and existing booking.
   *
   * @throws \Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException
   */
  public function editBooking() {
    throw new ExchangeNotSupportedException();
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

  /**
   * Send mail to Exchange.
   *
   * @param $to
   *   The resource that should receive the mail.
   * @param $subject
   *   The mails subject.
   * @param $body
   *   The message/body to send.
   * @param string $method
   *   The method to set in the header to Exchange.
   */
  private function sendMail($to, $subject, $body, $method = 'REQUEST') {
    // Create mail message.
    $message = $this->mailer->createMessage()
      ->setSubject($subject)
      ->setFrom($this->account['mail'])
      ->setSender($this->account['mail'])
      ->setReturnPath($this->account['mail'])
      ->setTo($to)
      ->setBody($body, 'text/calendar');

    // Set the required headers.
    $headers = $message->getHeaders();
    $type = $headers->get('Content-Type');
    $type->setValue('text/calendar');
    $type->setParameters(array(
      'charset' => 'utf-8',
      'method' => $method
    ));

    echo $message;

    // Send the mail.
    $this->mailer->send($message);
  }
}
