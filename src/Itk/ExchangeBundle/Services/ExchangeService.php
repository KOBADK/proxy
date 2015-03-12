<?php
/**
 * @file
 * Wrapper service for the more specialized exchanges services.
 *
 * This wrapper exists as the methods used to communication with Exchange is
 * split between sending ICal formatted mails and pull the Exchange server via
 * the EWS reset API.
 */

namespace Itk\ExchangeBundle\Services;

/**
 * Class ExchangeService
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeService {


  /**
   * Initialise ExchangeWebservice.
   *
   * @param string $host
   * Hostname of Exchange web service.
   * @param string $username
   * Username.
   * @param $password
   * Password.
   */
//  public function initExchangeWebservice($host, $username, $password) {
//    $this->ews = new ExchangeWebServices(
//      $host,
//      $username,
//      $password,
//      ExchangeWebServices::VERSION_2010
//    );
//  }
//

  /**
   * Get events for a resource.
   *
   * @param $resourceMail
   * Mail identifying the resource.
   *
   * @param $startDate
   * Start date to get events from.
   * @param $endDate
   * End date to get events from.
   *
   * @returns array
   * Array of events.
   *
   * @TODO: Implement and test this!
   */
//  public function listEventsForResource($resourceMail, $startDate, $endDate) {
//// Configure impersonation
//    $ei = new ExchangeImpersonationType();
//    $sid = new ConnectingSIDType();
//    $sid->PrimarySmtpAddress = $resourceMail;
//    $ei->ConnectingSID = $sid;
//    $this->ews->setImpersonation($ei);
//    $request = new FindItemType();
//    $request->Traversal = ItemQueryTraversalType::SHALLOW;
//    $request->ItemShape = new ItemResponseShapeType();
//    $request->ItemShape->BaseShape = DefaultShapeNamesType::DEFAULT_PROPERTIES;
//    $request->CalendarView = new CalendarViewType();
//    $request->CalendarView->StartDate = $startDate;
//    $request->CalendarView->EndDate = $endDate;
//    $request->ParentFolderIds = new NonEmptyArrayOfBaseFolderIdsType();
//    $request->ParentFolderIds->DistinguishedFolderId = new DistinguishedFolderIdType();
//    $request->ParentFolderIds->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::CALENDAR;
//    $response = $this->ews->FindItem($request);
//// Verify the response.
//    if ($response->ResponseMessages->FindItemResponseMessage->ResponseCode == 'NoError') {
//// Verify items.
//      if ($response->ResponseMessages->FindItemResponseMessage->RootFolder->TotalItemsInView > 0) {
//        return NULL;
//      }
//    }
//    return NULL;
//  }


}
