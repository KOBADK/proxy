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

use Itk\ExchangeBundle\Exceptions\ExchangeNotSupported;

/**
 * Class ExchangeBookingService
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeMailer {

  private $mailer;

  /**
   * Constructor
   *
   * @param $mailer
   *   The mailer used to send mail to Exchange.
   */
  public function __construct($mailer) {
    $this->mailer = $mailer;

    // @TODO HACK: Make this into a symfony library.
    require_once '../lib/iCalcreator/iCalcreator.class.php';
  }

  public function createBooking() {

  }

  public function cancelBooking() {
    throw new ExchangeNotSupported();
  }

  public function editBooking() {
    throw new ExchangeNotSupported();
  }

  private function sendMail($m) {
     $message = $this->mailer->createMessage()
      ->setSubject('You have Completed Registration!')
      ->setFrom('send@example.com')
      ->setTo('recipient@example.com')
      ->setBody($m, 'text/plain')

    ;
    $this->mailer->send($message);
  }

}
