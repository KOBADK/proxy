<?php
/**
 * @file
 * Contains the Itk ExchangeService.
 */

namespace Itk\ExchangeBundle\Services;

use Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException;
use Itk\ExchangeBundle\Exceptions\ExchangeSoapException;

/**
 * Class ExchangeWS
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeWebService {

  private $client;

  public function __construct(ExchangeSoapClientService $client) {
    $this->client = $client;
  }

  /**
   * Get list of rooms (resources) from Exchange.
   */
  public function getRessources() {
    // Get list of rooms from Exchange. Only rooms that the Exchange
    // administrators have allow you to get.
    // @see https://social.msdn.microsoft.com/Forums/office/en-US/4ff04c60-48c2-4a69-ab75-2383e73bfde2/e2010ewsxmljavahow-to-list-all-resource-mailboxes-meeting-room-in-exchange-web-service-2010?forum=exchangesvrdevelopment
    try {
      $xml = $this->client->request('GetRoomLists', '<m:GetRoomLists/>');
    }
    catch (ExchangeSoapException $exception) {
      // @TODO: do some error handling.
    }

    $dom = new \DOMDocument();
    $dom->loadXML($xml);

    // @TODO: Get the rooms and create resource entities?
  }

  /**
   * Get detailed information about a booking.
   *
   * @param $id
   *   The Exchange ID for the booking.
   * @param $changeKey
   *   The Exchange change key (revision id).
   */
  public function getBooking($id, $changeKey) {
    // Build XML body.
    $body = '<GetItem xmlns="http://schemas.microsoft.com/exchange/services/2006/messages">
      <ItemShape>
        <t:BaseShape>Default</t:BaseShape>
        <t:AdditionalProperties>
            <t:FieldURI FieldURI="item:TextBody" />
        </t:AdditionalProperties>
      </ItemShape>
      <ItemIds>
        <t:ItemId Id="' . $id . '" ChangeKey="' . $changeKey . '"/>
      </ItemIds>
    </GetItem>';

//    try {
      $xml = $this->client->request('GetItem', $body);
//    }
//    catch (ExchangeSoapException $exception) {
      // @TODO: do some error handling.
//    }

    $dom = new \DOMDocument();
    $dom->loadXML($xml);

    echo $xml;

    // @TODO: return bookings?
  }

  /**
   * Get bookings on a resource.
   *
   * @param $impersonationId
   *   The id of the resource (mail address).
   * @param $start
   *   Unix timestamp for the start date to query Exchange.
   * @param $end
   *   Unix timestamp for the end date to query Exchange.
   */
  public function getRessourceBookings($impersonationId, $start, $end) {
    // Build XML body.
    $body = '<FindItem  Traversal="Shallow" xmlns="http://schemas.microsoft.com/exchange/services/2006/messages">
      <ItemShape>
        <t:BaseShape>Default</t:BaseShape>
      </ItemShape>
      <CalendarView StartDate="' . date('Y-m-d\TH:i:s\Z', $start) . '" EndDate="' . date('Y-m-d\TH:i:s\Z', $end) . '"/>
      <ParentFolderIds>
        <t:DistinguishedFolderId Id="calendar"/>
      </ParentFolderIds>
    </FindItem>';

//    try {
      $xml = $this->client->request('FindItem', $body, $impersonationId);
//    }
//    catch (ExchangeSoapException $exception) {
      // @TODO: do some error handling.
//    }

    $dom = new \DOMDocument();
    $dom->loadXML($xml);

    echo $xml;

    // @TODO: return bookings?
  }
}
