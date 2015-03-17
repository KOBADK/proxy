<?php
/**
 * @file
 * Contains the Itk ExchangeService
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

  public function getRessource() {
    throw new ExchangeNotSupportedException();
  }

  public function getRessourceBookings() {
    throw new ExchangeNotSupportedException();
  }
}
