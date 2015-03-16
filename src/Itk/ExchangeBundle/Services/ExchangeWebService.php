<?php
/**
 * @file
 * Contains the Itk ExchangeService
 */

namespace Itk\ExchangeBundle\Services;

use Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException;

/**
 * Class ExchangeWS
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeWebService {

  private $client;

  public function __construct(ExchangeSoapClientService $client) {

    $this->client = $client;

//    $this->ews = new ExchangeWebServices($host, $username, $password, ExchangeWebServices::VERSION_2010);
  }

  public function getRessources() {
    // Debug options.
    $options = array(
      CURLOPT_PROXY => "http://127.0.0.1:8080/",
      CURLOPT_PROXYTYPE => 7,
    );

    $xml = $this->client->request('GetRoomLists', '<m:GetRoomLists/>', $options);
  }

  public function getRessource() {
    throw new ExchangeNotSupportedException();
  }
}
