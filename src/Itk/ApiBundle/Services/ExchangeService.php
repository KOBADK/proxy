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
  protected $ews;
  protected $bookingMail;
  protected $bookingUser;

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

    $this->ews = new ExchangeWebServices(
      $this->container->getParameter('ews_host'),
      $this->container->getParameter('ews_user'),
      $this->container->getParameter('ews_password'),
      ExchangeWebServices::VERSION_2010
    );

    $this->bookingMail = $this->container->getParameter('ews_booking_mail');
    $this->bookingUser = $this->container->getParameter('ews_booking_user');
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

  public function listAction($resourceMail, $startDate, $endDate) {
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
    if ($response->ResponseMessages->FindItemResponseMessage->ResponseCode == "NoError") {
      // Verify items.
      if ($response->ResponseMessages->FindItemResponseMessage->RootFolder->TotalItemsInView > 0) {
        return $this->helperService->generateResponse(200, $response->ResponseMessages->FindItemResponseMessage->RootFolder->Items->CalendarItem);
      }
    }
    return $this->render('ItkKobaBundle:Default:index.html.twig');
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
  public function sendBookingRequest(Booking $booking) {
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
      ORGANIZER;CN=" . $this->bookingUser . ":mailto:" . $this->bookingMail . "\r\n
      DESCRIPTION:" . $booking->getDescription() . "\r\n
      END:VEVENT\r\n
      END:VCALENDAR\r\n";

    // Send the e-mail.
    $success = mail($booking->getResource()->getMail(), $booking->getSubject(), $message, $this->ewsHeaders, "-f " . $booking->getUser()->getMail());

    if (!$success) {
      $booking->setStatusMessage('Mail not received by resource');

      $this->em->flush();
      return $this->helperService->generateResponse(503, null, array('message' => 'Booking request was not delivered to resource, try again'));
    }
    else {
      return $this->helperService->generateResponse(200, $booking);
    }
  }


  public function sendBookingTest() {
    $resource = new Resource();
    $resource->setName("res 1");
    $resource->setMail("test_loc@test.test");
    $resource->setExpire(10000000);
    $resource->setMailbox("bla");
    $resource->setRouting("blip");
    $this->em->persist($resource);

    $user = new User();
    $user->setStatus(true);
    $user->setMail("test@test.test");
    $user->setName("Test testesen");
    $user->setUuid("123");
    $this->em->persist($user);

    $booking = new Booking();
    $booking->setStartDateTime(new \DateTime("@1421749680"));
    $booking->setEndDateTime(  new \DateTime("@1421749880"));
    $booking->setDescription("fisk");
    $booking->setSubject("faks");
    $booking->setUser($user);
    $booking->setResource($resource);
    $this->em->persist($booking);

    $this->em->flush();

    return $this->sendBookingRequestEmail($booking);
  }
}