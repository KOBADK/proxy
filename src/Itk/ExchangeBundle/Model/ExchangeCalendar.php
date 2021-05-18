<?php

namespace Itk\ExchangeBundle\Model;

use Itk\ExchangeBundle\Entity\Resource;

/**
 * Class ExchangeCalendar
 *
 * @package Itk\ExchangeBundle\Model
 */
class ExchangeCalendar
{
    private $start;
    private $end;
    private $resource;
    private $bookings;

    /**
     * @param Resource $resource
     * @param $start
     * @param $end
     * @param array $bookings
     */
    public function __construct(
        Resource $resource,
        $start,
        $end,
        $bookings = array()
    ) {
        $this->resource = $resource;
        $this->start = $start;
        $this->end = $end;
        $this->bookings = $bookings;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $length = 120;
        $str = [];
        $str[] = str_repeat('-', $length);
        $str[] = '| From: '. date('c'.$this->getStart()).' To: '.date('c', $this->getEnd());
        $str[] = str_repeat('-', $length);
        foreach ($this->getBookings() as $booking) {
            $str[] = '| '.date('c', $booking->getStart()).' -> '.$booking->getSubject();
        }
        $str[] = str_repeat('-', $length);

        return implode("\n", $str);
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param mixed $resource
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return mixed
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * @param mixed $bookings
     */
    public function setBookings($bookings)
    {
        $this->bookings = $bookings;
    }

    /**
     * @param \Itk\ExchangeBundle\Model\ExchangeBooking $booking
     */
    public function addBooking(ExchangeBooking $booking)
    {
        $this->bookings[] = $booking;
    }
}
