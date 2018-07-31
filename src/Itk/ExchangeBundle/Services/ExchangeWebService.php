<?php
/**
 * @file
 * Contains the Itk ExchangeService.
 */

namespace Itk\ExchangeBundle\Services;

use Itk\ExchangeBundle\Model\ExchangeBooking;
use Itk\ExchangeBundle\Model\ExchangeCalendar;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ExchangeWS
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeWebService
{

    private $client;

    public function __construct(ExchangeSoapClientService $client)
    {
        $this->client = $client;
    }

    /**
     * Get detailed information about a booking.
     *
     * @param $resource
     *   The resource to impersonate.
     * @param $id
     *   The Exchange ID for the booking.
     * @param $changeKey
     *   The Exchange change key (revision id).
     *
     * @return bool|\Itk\ExchangeBundle\Model\ExchangeBooking
     *   If the booking exists return it else FALSE.
     */
    public function getBooking($resource, $id, $changeKey)
    {
        // Build XML body.
        $body = '<GetItem xmlns="http://schemas.microsoft.com/exchange/services/2006/messages">
      <ItemShape>
        <t:BaseShape>Default</t:BaseShape>
        <t:AdditionalProperties>
            <t:FieldURI FieldURI="item:TextBody" />
        </t:AdditionalProperties>
      </ItemShape>
      <ItemIds>
        <t:ItemId Id="'.$id.'" ChangeKey="'.$changeKey.'"/>
      </ItemIds>
    </GetItem>';

        // Send request to EWS.
        $xml = $this->client->request('GetItem', $body, $resource->getMail());

        $doc = new \DOMDocument();
        $doc->loadXML($xml);

        /**
         * @TODO: Look for error message and log theme.
         */

        // Parse the booking if it exists.
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace(
            't',
            'http://schemas.microsoft.com/exchange/services/2006/types'
        );
        $items = $xpath->query('//t:CalendarItem');
        if ($items->length) {
            return $this->parseBookingXML($items->item(0), $xpath);
        }

        // Booking not found.
        return false;
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
    public function getRessourceBookings($resource, $from, $to)
    {
        $calendar = new ExchangeCalendar($resource, $from, $to);

        // Build XML body.
        $body = '<FindItem  Traversal="Shallow" xmlns="http://schemas.microsoft.com/exchange/services/2006/messages">
      <ItemShape>
        <t:BaseShape>Default</t:BaseShape>
      </ItemShape>
      <CalendarView StartDate="'.date('c', $from).'" EndDate="'.date('c', $to).'"/>
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
         *
         * <m:FindItemResponseMessage ResponseClass="Success">
         *   <m:ResponseCode>NoError</m:ResponseCode>
         *   <m:RootFolder TotalItemsInView="0" IncludesLastItemInRange="true">
         *     <t:Items/>
         *   </m:RootFolder>
         * </m:FindItemResponseMessage>
         */

        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace(
            't',
            'http://schemas.microsoft.com/exchange/services/2006/types'
        );

        // Find the calendar items.
        $calendarItems = $xpath->query('//t:CalendarItem');

        foreach ($calendarItems as $calendarItem) {
            $calendar->addBooking(
                $this->parseBookingXML($calendarItem, $xpath)
            );
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
    private function parseBookingXML(\DOMNode $calendarItem, \DOMXPath $xpath)
    {
        $itemId = $xpath->evaluate('./t:ItemId', $calendarItem);

        $booking = new ExchangeBooking(
            $itemId->item(0)->getAttribute('Id'),
            $itemId->item(0)->getAttribute('ChangeKey')
        );
        $booking->setSubject(
            $xpath->evaluate('./t:Subject', $calendarItem)->item(0)->nodeValue
        );

        // Set timestamps.
        $booking->setStart(
            strtotime(
                $xpath->evaluate('./t:Start', $calendarItem)->item(0)->nodeValue
            )
        );
        $booking->setEnd(
            strtotime(
                $xpath->evaluate('./t:End', $calendarItem)->item(0)->nodeValue
            )
        );


        $body = $xpath->evaluate('./t:TextBody', $calendarItem);
        if ($body->length) {
            $this->parseBodyField($booking, (string)$body->item(0)->nodeValue);
        }

        return $booking;
    }

    /**
     * Parse body.
     *
     * @param \Itk\ExchangeBundle\Model\ExchangeBooking $exchangeBooking
     * @param $body
     */
    private function parseBodyField(ExchangeBooking $exchangeBooking, $body)
    {
        if (preg_match('/<!-- KOBA (.+) KOBA -->/s', $body, $matches)) {
            if (isset($matches[1])) {
                // Decode booking information.
                $encoders = array(new XmlEncoder(), new JsonEncoder());
                $normalizers = array(new GetSetMethodNormalizer());
                $normalizers[0]->setIgnoredAttributes(
                    array('resource', 'exchangeId')
                );
                $serializer = new Serializer($normalizers, $encoders);

                $json = base64_decode($matches[1]);

                $exchangeBooking->setTypeKoba();
                $exchangeBooking->setBody(
                    $serializer->deserialize(
                        $json,
                        'Itk\ExchangeBundle\Entity\Booking',
                        'json'
                    )
                );
            }
        }
    }
}
