<?php
/**
 * @file
 * Contains the Itk ExchangeService
 */

namespace Itk\ExchangeBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Koba\MainBundle\Entity\Booking;
use Koba\MainBundle\Entity\Resource;
use Koba\MainBundle\Entity\User;
use PhpEws\ExchangeWebServices;
use PhpEws\EWSType\ExchangeImpersonationType;
use PhpEws\EWSType\ConnectingSIDType;
use PhpEws\EWSType\FindItemType;
use PhpEws\EWSType\ItemQueryTraversalType;
use PhpEws\EWSType\ItemResponseShapeType;
use PhpEws\EWSType\DefaultShapeNamesType;
use PhpEws\EWSType\CalendarViewType;
use PhpEws\EWSType\NonEmptyArrayOfBaseFolderIdsType;
use PhpEws\EWSType\DistinguishedFolderIdType;
use PhpEws\EWSType\DistinguishedFolderIdNameType;
use Symfony\Component\Intl\Exception\NotImplementedException;

/**
 * Class ExchangeService
 *
 * @package Itk\ApiBundle\Services
 */
class ExchangeService {
  const EWS_HEADERS = "Content-Type:text/calendar; charset=utf-8; method=REQUEST\r\nContent-Type: text/plain; charset=\"utf-8\" \r\n";

  protected $ews;
  protected $bookingMail;
  protected $bookingUser;

  /**
   * Constructor
   */
  public function __construct() {

  }

  /**
   * Initialise ExchangeWebservice.
   *
   * @param string $host
   *   Hostname of Exchange web service.
   * @param string $username
   *   Username.
   * @param $password
   *   Password.
   */
  public function initExchangeWebservice($host, $username, $password) {
    $this->ews = new ExchangeWebServices(
      $host,
      $username,
      $password,
      ExchangeWebServices::VERSION_2010
    );
  }

  /**
   * Set the exchange user.
   *
   * @param $name
   *   Name of the user.
   * @param $mail
   *   Mail for the user.
   */
  public function setExchangeUser($name, $mail) {
    $this->bookingUser = $name;
    $this->bookingMail = $mail;
  }

  /**
   * Get a resource from exchange and update the local resource information.
   *
   * @param string $mail
   *   The mail that identifies the resource in Exchange
   *
   * @TODO: Implement this.
   */
  public function getResource($mail) {
    throw new NotImplementedException('not implemented');
  }

  /**
   * Get events for a resource.
   *
   * @param $resourceMail
   *   Mail identifying the resource.
   *
   * @param $startDate
   *   Start date to get events from.
   * @param $endDate
   *   End date to get events from.
   *
   * @returns array
   *   Array of events.
   *
   * @TODO: Implement and test this!
   */
  public function listEventsForResource($resourceMail, $startDate, $endDate) {
    // Configure impersonation
    $ei = new ExchangeImpersonationType();
    $sid = new ConnectingSIDType();
    $sid->PrimarySmtpAddress = $resourceMail;
    $ei->ConnectingSID = $sid;
    $this->ews->setImpersonation($ei);
    $request = new FindItemType();
    $request->Traversal = ItemQueryTraversalType::SHALLOW;
    $request->ItemShape = new ItemResponseShapeType();
    $request->ItemShape->BaseShape = DefaultShapeNamesType::DEFAULT_PROPERTIES;
    $request->CalendarView = new CalendarViewType();
    $request->CalendarView->StartDate = $startDate;
    $request->CalendarView->EndDate = $endDate;
    $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
    $request->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType();
    $request->ParentFolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::CALENDAR;
    $response = $this->ews->FindItem($request);

    // Verify the response.
    if ($response->ResponseMessages->FindItemResponseMessage->ResponseCode == 'NoError') {
      // Verify items.
      if ($response->ResponseMessages->FindItemResponseMessage->RootFolder->TotalItemsInView > 0) {
        return NULL;
      }
    }

    return NULL;
  }

  /**
   * Send a booking request to the resource.
   *
   * Creates a vCard of the event to send to the resource.
   *
   * iCalendar doc (p. 52 icalbody):
   * https://www.ietf.org/rfc/rfc2445.txt
   *
   * @param Booking $booking
   *   The booking to attempt to make
   *
   * @returns boolean
   *   Success?
   *
   * @TODO: What to do when the mail has been sent? Emit an event?
   * @TODO: Unified way of handling date formats.
   */
  public function sendBookingRequest(Booking $booking) {
    $timestamp = gmdate('Ymd\THis+01');
    $uid  = $timestamp . '-' . $booking->getUser()->getMail();
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
