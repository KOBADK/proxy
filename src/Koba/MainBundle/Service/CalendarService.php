<?php
/**
 * @file
 * Contains the CalendarService.
 */

namespace Koba\MainBundle\Service;

use Itk\ExchangeBundle\Entity\Resource;
use Itk\ExchangeBundle\Services\ExchangeService;
use Koba\MainBundle\Entity\ApiKey;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class CalendarService
 *
 * @package Koba\MainBundle
 */
class CalendarService
{
    const DSS = 'dss:';
    const RC = 'rc:';

    protected $cache;
    protected $exchangeService;

    /**
     * Constructor
     *
     * @param ExchangeService $exchangeService
     * @param CacheInterface $cache
     */
    public function __construct(
        ExchangeService $exchangeService,
        CacheInterface $cache
    ) {
        $this->exchangeService = $exchangeService;
        $this->cache = $cache;
    }

    /**
     * Get cache key.
     *
     * @param $key
     * @return bool|mixed
     */
    public function getCacheKey($key) {
        try {
            return $this->cache->get(sha1($key));
        }
        catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * Set cache key.
     *
     * @param $key
     * @param $value
     * @param null $ttl
     * @return bool
     */
    private function setCacheKey($key, $value, $ttl = null) {
        try {
            return $this->cache->set(sha1($key), $value, $ttl);
        }
        catch (InvalidArgumentException $e) {
            return false;
        }
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return array
     *   The bookings for the resource.
     */
    public function getCalendar(
        ApiKey $apiKey,
        $groupId,
        Resource $resource,
        $resourceConfiguration,
        $from,
        $to
    ) {
        // Get cache id
        $cacheId = $apiKey->getApiKey().':'.$groupId.':'.$resource->getMail().':'.$from.':'.$to;

        // Get the bookings from the cache.
        $bookings = $this->getCacheKey($cacheId);

        // If the entry exists return it.
        if ($bookings) {
            return json_decode($bookings);
        } else {
            $bookings = array();

            // Dependant on the resourceConfiguration['display'] we get bookings in various ways.
            //   DSS - from the dss XML file
            //   RC  - from the rc XML file
            //   RC_FREE_BUSY - RC with free/busy
            //   FREE_BUSY - from exchange, only free/busy times
            //   BOOKED_BY - shows "Booked by [first_name]" as title
            //   KOBA - all data from a booking made in KOBA
            switch ($resourceConfiguration['display']) {
                case 'DSS':
                    $cacheEntry = $this->getCacheKey(CalendarService::DSS.$resource->getName());

                    if ($cacheEntry) {
                        $xmlBookings = json_decode($cacheEntry);

                        $bookings = $this->processXmlBookings(
                            $xmlBookings,
                            $from,
                            $to,
                            $resource
                        );
                    }
                    break;
                case 'RC':
                    $cacheEntry = $this->getCacheKey(CalendarService::RC.$resource->getName());

                    if ($cacheEntry) {
                        $xmlBookings = json_decode($cacheEntry);

                        $bookings = $this->processXmlBookings(
                            $xmlBookings,
                            $from,
                            $to,
                            $resource
                        );
                    }
                    break;
                case 'RC_FREE_BUSY':
                    $eventNames = array();

                    $cacheEntry = $this->getCacheKey(
                        CalendarService::RC.$resource->getName()
                    );
                    if ($cacheEntry) {
                        $rcBookings = json_decode($cacheEntry);

                        // Make associative array from start/end time to event name, for quick lookups.
                        if ($rcBookings) {
                            foreach ($rcBookings as $rcBooking) {
                                $eventNames[$rcBooking->start_time."-".$rcBooking->end_time] = $rcBooking->event_name;
                            }
                        }
                    }

                    // Get free/busy.
                    $exchangeCalendar = $this->exchangeService->getResourceBookings(
                        $resource,
                        $from,
                        $to,
                        false
                    );

                    // Set event name from quick look up array.
                    foreach ($exchangeCalendar->getBookings() as $booking) {
                        $eventName = $booking->getStart().'-'.$booking->getEnd();

                        $obj = (object)array(
                            'start_time' => $booking->getStart(),
                            'end_time' => $booking->getEnd(),
                            'event_name' => isset($eventNames[$eventName]) ? $eventNames[$eventName] : null,
                            'resource_id' => $resource->getName(),
                            'resource_alias' => $resource->getAlias(),
                        );

                        $bookings[] = $obj;
                    }
                    break;
                case 'FREE_BUSY':
                    $exchangeCalendar = $this->exchangeService->getResourceBookings(
                        $resource,
                        $from,
                        $to,
                        false
                    );

                    foreach ($exchangeCalendar->getBookings() as $booking) {
                        $bookings[] = (object)array(
                            'start_time' => $booking->getStart(),
                            'end_time' => $booking->getEnd(),
                            'resource_id' => $resource->getName(),
                            'resource_alias' => $resource->getAlias(),
                        );
                    }
                    break;
                case 'BOOKED_BY':
                    $exchangeCalendar = $this->exchangeService->getResourceBookings(
                        $resource,
                        $from,
                        $to,
                        true
                    );

                    foreach ($exchangeCalendar->getBookings() as $booking) {
                        $bookings[] = (object)array(
                            'start_time' => $booking->getStart(),
                            'end_time' => $booking->getEnd(),
                            'name' => $booking->getBody()->getName(
                            ),
                            'resource_id' => $resource->getName(),
                            'resource_alias' => $resource->getAlias(
                            ),
                        );
                    }
                    break;
                case 'KOBA':
                    $exchangeCalendar = $this->exchangeService->getResourceBookings(
                        $resource,
                        $from,
                        $to,
                        true
                    );

                    // @TODO: Merge with free/busy or other source.

                    foreach ($exchangeCalendar->getBookings() as $booking) {
                        $bookings[] = (object)array(
                            'start_time' => $booking->getStartTime(),
                            'end_time' => $booking->getEndTime(),
                            'event_name' => $booking->getBody()->getSubject(),
                            'event_description' => $booking->getBody()->getDescription(),
                            'name' => $booking->getBody()->getName(),
                            'resource_id' => $resource->getName(),
                            'resource_alias' => $resource->getAlias(),
                        );
                    }
                    break;
            }

            // Save bookings in the cache.
            $this->setCacheKey($cacheId, json_encode($bookings), 300);

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
    private function filterBookings($bookings, $from, $to)
    {
        $results = array();

        foreach ($bookings as $booking) {
            if ($booking->start_time < $to && $booking->end_time > $from) {
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
    private function processXmlBookings($bookings, $from, $to, $resource)
    {
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
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function updateXmlData()
    {
        // Get DSS XML data and add to cache.
        $xmlData = $this->exchangeService->getExchangeDssXmlData();
        foreach ($xmlData as $key => $value) {
            // Cache and expire after 1 day
            $this->setCacheKey(CalendarService::DSS.$key, json_encode($value), 86400);
        }

        // Get RC XML data and add to cache.
        $xmlData = $this->exchangeService->getExchangeRcXmlData();
        foreach ($xmlData as $key => $value) {
            // Cache and expire after 1 day
            $this->setCacheKey(CalendarService::RC.$key, json_encode($value), 86400);
        }
    }
}
