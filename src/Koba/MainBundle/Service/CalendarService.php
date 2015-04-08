<?php
/**
 * @file
 * Contains the CalendarService.
 */

namespace Koba\MainBundle\Service;

use Itk\ExchangeBundle\Services\ExchangeService;

/**
 * Class CalendarService
 *
 * @package Koba\MainBundle
 */
class CalendarService {
  protected $redis;
  protected $exchangeService;

  public function __construct(ExchangeService $exchangeService, $redis) {
    $this->exchangeService = $exchangeService;
    $this->redis = $redis;
  }

  /**
   * Get the calendar events for a given resource.
   *
   * @param string $resourceMail
   *   The mail (id) of the resource.
   * @param string $resourceName
   *   The name of the resource.
   * @param integer $interestPeriod
   *   The number of seconds to look for data for from exchange.
   *   Defaults to 7 days = 604800 seconds.
   *
   * @return array
   *   The bookings for the resource.
   */
  public function getCalendar($resourceMail, $resourceName, $interestPeriod = 604800) {
    $bookings = $this->redis->get('resources:' . $resourceMail . ':' . $interestPeriod);

    // If the entry exists return.
    if ($bookings) {
      return json_decode($bookings);
    }
    else {
      // @TODO: Merge exchangeData and $xmlBookings
      //$exchangeData = $this->exchangeService->getBookingsForResource($resourceMail, $interestPeriod);

      $xmlBookings = json_decode($this->redis->get('xml:' . $resourceName));

      $this->redis->set('resources:' . $resourceMail . ':' . $interestPeriod, json_encode($xmlBookings));
      $this->redis->expire('resources:' . $resourceMail . ':' . $interestPeriod, 300);

      return $xmlBookings;
    }
  }

  /**
   * Update the xml data.
   */
  public function updateXMLData() {
    $xmlData = $this->exchangeService->getExchangeXMLData('./web/test.xml');

    foreach ($xmlData as $key => $value) {
      $this->redis->set('xml:' . $key, json_encode($value));
    }
  }
}
