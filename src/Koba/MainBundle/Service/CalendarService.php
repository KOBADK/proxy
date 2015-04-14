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
   * @param ApiKey $apiKey
   *   The api key.
   * @param string $groupId
   *   The group id.
   * @param Resource $resource
   *   The resource.
   * @param integer $from
   *   Start interest time. Unix timestamp.
   * @param integer $to
   *   End interest time. Unix timestamp.
   *
   * @return array
   *   The bookings for the resource.
   */
  public function getCalendar(ApiKey $apiKey, $groupId, Resource $resource, $from, $to) {
    throw new ExchangeNotSupportedException();

    /*$bookings = $this->redis->get('resources:' . $resourceMail . ':' . $interestPeriod);

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
    */
  }

  /**
   * Update the xml data.
   */
  public function updateXMLData() {
    $xmlData = $this->exchangeService->getExchangeXMLData();

    foreach ($xmlData as $key => $value) {
      $this->redis->set('xml:' . $key, json_encode($value));
    }
  }
}
