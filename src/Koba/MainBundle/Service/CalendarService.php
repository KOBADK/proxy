<?php
/**
 * @file
 * Contains the CalendarService.
 */

namespace Koba\MainBundle\Service;

use Itk\ExchangeBundle\Entity\Resource;
use Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException;
use Itk\ExchangeBundle\Services\ExchangeService;
use Koba\MainBundle\Entity\ApiKey;
use Predis\NotSupportedException;

/**
 * Class CalendarService
 *
 * @package Koba\MainBundle
 */
class CalendarService {
  protected $cache;
  protected $exchangeService;

  /**
   * Constructor
   *
   * @param ExchangeService $exchangeService
   * @param CacheInterface $cache
   */
  public function __construct(ExchangeService $exchangeService, CacheInterface $cache) {
    $this->exchangeService = $exchangeService;
    $this->cache = $cache;
  }

  /**
   * Get the calendar events for a given resource.
   *
   * @param ApiKey $apiKey
   *   The api key.
   * @param string $groupId
   *   The group id.
   * @param Resource $resource
   *   The resource.
   * @param array $resourceConfiguration
   *   Configuration object for the resource.
   * @param integer $from
   *   Start interest time. Unix timestamp.
   * @param integer $to
   *   End interest time. Unix timestamp.
   *
   * @throws NotSupportedException
   *
   * @return array
   *   The bookings for the resource.
   */
  public function getCalendar(ApiKey $apiKey, $groupId, $resource, $resourceConfiguration, $from, $to) {
    // Get cache id
    $cacheId = $apiKey->getApiKey() . ':' . $groupId . ':' . $resource->getMail() . ':' . $from . ':' . $to;

    // Get the bookings from the cache.
    $bookings = $this->cache->get($cacheId);

    // If the entry exists return it.
    if ($bookings) {
      return json_decode($bookings);
    }
    else {
      $xmlBookings = array();

      // Dependant on the resourceConfiguration['display'] we get bookings in various ways.
      //   DSS - from the dss XML file
      //   RC  - from the rc XML file
      //   FREE_BUSY - from exchange, only free/busy times
      //   BOOKED_BY - shows "Booked by [first_name]" as title
      //   KOBA_BOOKING - all data from a booking made in KOBA
      //   SAFE_TITLE - the title is from a special tag added to the body of a
      //     booking made from exchange.
      if ($resourceConfiguration['display'] === 'DSS') {
        $xmlBookings = json_decode($this->cache->get('dss:' . $resource->getName()));
      }
      else if ($resourceConfiguration['display'] === 'RC') {
        $xmlBookings = json_decode($this->cache->get('rc:' . $resource->getName()));
      }
      else if ($resourceConfiguration['display'] === 'FREE_BUSY') {
        throw new NotSupportedException();
      }
      else if ($resourceConfiguration['display'] === 'BOOKED_BY') {
        throw new NotSupportedException();
      }
      else if ($resourceConfiguration['display'] === 'KOBA_BOOKING') {
        throw new NotSupportedException();
      }
      else if ($resourceConfiguration['display'] === 'SAFE_TITLE') {
        throw new NotSupportedException();
      }

      // Save bookings in the cache.
      $this->cache->set($cacheId, json_encode($xmlBookings), 300);

      return $xmlBookings;
    }
  }

  /**
   * Update the xml data.
   */
  public function updateXMLData() {
    // Get DSS XML data and add to cache.
    $xmlData = $this->exchangeService->getExchangeDssXmlData();
    foreach ($xmlData as $key => $value) {
      $this->cache->set('dss:' . $key, json_encode($value));
    }

    // Get RC XML data and add to cache.
    $xmlData = $this->exchangeService->getExchangeRcXmlData();
    foreach ($xmlData as $key => $value) {
      $this->cache->set('rc:' . $key, json_encode($value));
    }
  }
}
