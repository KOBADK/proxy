<?php
/**
 * @file
 * Contains the CalendarService.
 */

namespace Koba\MainBundle\Service;

use Itk\ExchangeBundle\Entity\Resource;
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
  public function getCalendar(ApiKey $apiKey, $groupId, Resource $resource, $resourceConfiguration, $from, $to) {
    // Get cache id
    $cacheId = $apiKey->getApiKey() . ':' . $groupId . ':' . $resource->getMail() . ':' . $from . ':' . $to;

    // Get the bookings from the cache.
    $bookings = $this->cache->get($cacheId);

    // If the entry exists return it.
    if ($bookings) {
      return json_decode($bookings);
    }
    else {
      $bookings = array();

      // Dependant on the resourceConfiguration['display'] we get bookings in various ways.
      //   DSS - from the dss XML file
      //   RC  - from the rc XML file
      //   FREE_BUSY - from exchange, only free/busy times
      //   BOOKED_BY - shows "Booked by [first_name]" as title
      //   KOBA_BOOKING - all data from a booking made in KOBA
      if ($resourceConfiguration['display'] === 'DSS') {
        $xmlBookings = json_decode($this->cache->get('dss:' . $resource->getName()));

        if ($xmlBookings) {
          $bookings = $this->processXmlBookings($xmlBookings, $from, $to, $resource);
        }
      }
      else if ($resourceConfiguration['display'] === 'RC') {
        $xmlBookings = json_decode($this->cache->get('rc:' . $resource->getName()));

        if ($xmlBookings) {
          $bookings = $this->processXmlBookings($xmlBookings, $from, $to, $resource);
        }
      }
      else if ($resourceConfiguration['display'] === 'FREE_BUSY') {
        $exchangeCalendar = $this->exchangeService->getResourceBookings($resource, $from, $to, FALSE);

        foreach ($exchangeCalendar->getBookings() as $booking) {
          $bookings[] = (object) array(
            'start_time' => $booking->getStart(),
            'end_time' => $booking->getEnd(),
            'resource_id' => $resource->getName(),
            'resource_alias' => $resource->getAlias(),
          );
        }
      }
      else if ($resourceConfiguration['display'] === 'BOOKED_BY') {
        $exchangeCalendar = $this->exchangeService->getResourceBookings($resource, $from, $to, TRUE);

        foreach ($exchangeCalendar->getBookings() as $booking) {
          $bookings[] = (object) array(
            'start_time' => $booking->getStart(),
            'end_time' => $booking->getEnd(),
            'name' => $booking->getBody()->getName(),
            'resource_id' => $resource->getName(),
            'resource_alias' => $resource->getAlias(),
          );
        }
      }
      else if ($resourceConfiguration['display'] === 'KOBA') {
        $exchangeCalendar = $this->exchangeService->getResourceBookings($resource, $from, $to, TRUE);

        // @TODO: Merge with free/busy or other source.

        foreach ($exchangeCalendar->getBookings() as $booking) {
          $bookings[] = (object) array(
            'start_time' => $booking->getStartTime(),
            'end_time' => $booking->getEndTime(),
            'event_name' => $booking->getBody()->getSubject(),
            'event_description' => $booking->getBody()->getDescription(),
            'name' => $booking->getBody()->getName(),
            'resource_id' => $resource->getName(),
            'resource_alias' => $resource->getAlias(),
          );
        }
      }
      else {
        throw new NotSupportedException();
      }

      // Save bookings in the cache.
      $this->cache->set($cacheId, json_encode($bookings), 300);

      return $bookings;
    }
  }

  /**
   * Filter bookings, so that only bookings that occur between from and end
   *   are left.
   *
   * @param $bookings
   *   Array of bookings.
   * @param integer $from
   *   From (unix timestamp)
   * @param integer $to
   *   To (unix timestamp)
   * @return array
   *   The filtered array of bookings.
   */
  private function filterBookings($bookings, $from, $to) {
    $results = array();

    foreach ($bookings as $booking) {
      if ($booking->start_time > $from && $booking->end_time < $to) {
        $results[] = $booking;
      }
    }

    return $results;
  }

  /**
   * Process bookings from XML RC or DSS files.
   *
   * @param $bookings
   *   Array of bookings
   * @param $from
   *   From (unix timestamp)
   * @param $to
   *   To (unix timestamp)
   * @param Resource $resource
   *   The resource.
   *
   * @return array
   *   The processed array of bookings.
   */
  private function processXmlBookings($bookings, $from, $to, $resource) {
    // Filter out bookings that are not from between $from and $to.
    $bookings = $this->filterBookings($bookings, $from, $to);

    // Set resource alias.
    foreach ($bookings as $booking) {
      $booking->resource_alias = $resource->getAlias();
    }

    return $bookings;
  }

  /**
   * Update the xml data.
   *
   * Used by cron process to cache data from the xml files.
   */
  public function updateXmlData() {
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
