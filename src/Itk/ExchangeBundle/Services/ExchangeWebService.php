<?php
/**
 * @file
 * Contains the Itk ExchangeService.
 */

namespace Itk\ExchangeBundle\Services;

use Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException;
use Itk\ExchangeBundle\Exceptions\ExchangeSoapException;
use Itk\ExchangeBundle\Model\ExchangeBooking;
use Itk\ExchangeBundle\Model\ExchangeCalendar;

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
   * Get detailed information about a booking.
   *
   * @param $id
   *   The Exchange ID for the booking.
   * @param $changeKey
   *   The Exchange change key (revision id).
   *
   * @return bool|\Itk\ExchangeBundle\Model\ExchangeBooking
   *   If the booking exists return it else FALSE.
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

    // Send request to EWS.
    $xml = $this->client->request('GetItem', $body);

    $doc = new \DOMDocument();
    $doc->loadXML($xml);

    /**
     * @TODO: Look for error message and log theme.
     */

    // Parse the booking if it exists.
    $xpath = new \DOMXPath($doc);
    $xpath->registerNamespace('t', 'http://schemas.microsoft.com/exchange/services/2006/types');
    $items = $xpath->query('//t:CalendarItem');
    if ($items->length) {
      return $this->parseBookingXML($items->item(0), $xpath);
    }

    // Booking not found.
    return FALSE;
  }

  /**
   * Get bookings on a resource.
   *
   * @param \Itk\ExchangeBundle\Entity\Resource $resource
   *   The resource to list.
   * @param $from
   *   Unix timestamp for the start date to query Exchange.
   * @param $to
   *   Unix timestamp for the end date to query Exchange.
   *
   * @return ExchangeCalendar
   *   Exchange calender with all bookings in the interval.
   */
  public function getRessourceBookings($resource, $from, $to) {
    $calendar = new ExchangeCalendar($resource, $from, $to);

    // Build XML body.
    $body = '<FindItem  Traversal="Shallow" xmlns="http://schemas.microsoft.com/exchange/services/2006/messages">
      <ItemShape>
        <t:BaseShape>Default</t:BaseShape>
      </ItemShape>
      <CalendarView StartDate="' . date('Y-m-d\TH:i:s\Z', $from) . '" EndDate="' . date('Y-m-d\TH:i:s\Z', $to) . '"/>
      <ParentFolderIds>
        <t:DistinguishedFolderId Id="calendar"/>
      </ParentFolderIds>
    </FindItem>';

    // Send request to EWS.
    $xml = $this->client->request('FindItem', $body, $resource->getMail());

    // Parse the response.
    $doc = new \DOMDocument();
    $doc->loadXML($xml);

    /**
     * @TODO: Look for error message and log theme.
     */

    $xpath = new \DOMXPath($doc);
    $xpath->registerNamespace('t', 'http://schemas.microsoft.com/exchange/services/2006/types');

    // Find the calendar items.
    $calendarItems = $xpath->query('//t:CalendarItem');

    foreach ($calendarItems as $calendarItem) {
      $calendar->addBooking($this->parseBookingXML($calendarItem, $xpath));
    }

    return $calendar;
  }

  /**
   * Parse DOMNode with calendarItem data.
   *
   * @param \DOMNode $calendarItem
   *   Node with calendar item data from XML.
   *
   * @return \Itk\ExchangeBundle\Model\ExchangeBooking
   *   The parsed Exchange booking object.
   */
  private function parseBookingXML(\DOMNode $calendarItem, \DOMXPath $xpath) {
    $itemId = $xpath->evaluate('./t:ItemId', $calendarItem);

    $booking = new ExchangeBooking($itemId->item(0)->getAttribute('Id'), $itemId->item(0)->getAttribute('ChangeKey'));
    $booking->setSubject($xpath->evaluate('./t:Subject', $calendarItem)->item(0)->nodeValue);

    // Set timestamps.
    $booking->setStart(strtotime($xpath->evaluate('./t:Start', $calendarItem)->item(0)->nodeValue));
    $booking->setEnd(strtotime($xpath->evaluate('./t:End', $calendarItem)->item(0)->nodeValue));

    $body = $xpath->evaluate('./t:TextBody', $calendarItem);
    if ($body->length) {
      $booking->setBody((string) $body->item(0)->nodeValue);
    }

    return $booking;
  }
}
