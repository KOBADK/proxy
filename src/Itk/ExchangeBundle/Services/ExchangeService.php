<?php
/**
 * @file
 * Wrapper service for the more specialized exchanges services.
 *
 * This wrapper exists as the methods used to communication with Exchange is
 * split between sending ICal formatted mails and pull the Exchange server via
 * the EWS reset API.
 */

namespace Itk\ExchangeBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Itk\ExchangeBundle\Entity\Resource;
use Itk\ExchangeBundle\Repository\ResourceRepository;
use Itk\ExchangeBundle\Entity\Booking;
use Itk\ExchangeBundle\Model\ExchangeBooking;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ExchangeService
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeService
{
    protected $exchangeADService;
    protected $resourceRepository;
    protected $entityManager;
    protected $exchangeMailService;
    protected $exchangeXMLService;
    protected $exchangeWebService;

    public function __construct(
        ExchangeADService $exchangeADService,
        ResourceRepository $resourceRepository,
        ExchangeMailService $exchangeMailService,
        ExchangeXMLService $exchangeXMLService,
        ExchangeWebService $exchangeWebService,
        EntityManagerInterface $entityManager
    ) {
        $this->exchangeADService = $exchangeADService;
        $this->resourceRepository = $resourceRepository;
        $this->exchangeMailService = $exchangeMailService;
        $this->exchangeXMLService = $exchangeXMLService;
        $this->exchangeWebService = $exchangeWebService;
        $this->entityManager = $entityManager;
    }

    /**
     * Get all resources from Exchange.
     *
     * @return array
     */
    public function getResources()
    {
        return $this->resourceRepository->findAll();
    }

    /**
     * Get resources from Exchange.
     *
     * @param $mail
     *   Mail of the resource.
     *
     * @return object|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getResourceByMail($mail)
    {
        return $this->resourceRepository->findOneByMail($mail);
    }

    /**
     * Refresh the available resource entities.
     */
    public function refreshResources()
    {
        $resources = $this->exchangeADService->getResources();

        // @TODO: Remove resources that are not in the list from AD.

        foreach ($resources as $key => $value) {
            $resource = $this->resourceRepository->findOneByMail($key);

            if (!$resource) {
                $resource = new Resource($key, $value);
                $this->entityManager->persist($resource);
            } else {
                $resource->setName($value);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Set the alias for a resource.
     *
     * @param $resourceMail
     *   Mail of the resource
     * @param $alias
     *   Alias
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function setResourceAlias($resourceMail, $alias)
    {
        $resource = $this->resourceRepository->findOneByMail($resourceMail);
        $resource->setAlias($alias);
        $this->entityManager->flush();
    }

    /**
     * Get bookings for a given resource.
     *
     * @param Resource $resource
     *   The mail of the resource.
     * @param int $from
     *   The the start of the interval as unix timestamp.
     * @param int $to
     *   The the end of the interval as unix timestamp.
     * @param bool $enrich
     *   Enrich the result with information form the bookings body.
     *
     * @return \Itk\ExchangeBundle\Model\ExchangeCalendar
     *   Exchange calendar object with bookings for the interval.
     */
    public function getResourceBookings(
        Resource $resource,
        $from,
        $to,
        $enrich = true
    ) {
        // Get basic calendar information.
        $calendar = $this->exchangeWebService->getRessourceBookings(
            $resource,
            $from,
            $to
        );

        // @TODO: Rename the bookings -> ExchangeBookings to remove mis-use to booking entity.
        // Check if body information should be included.
        if ($enrich) {
            $bookings = $calendar->getBookings();
            foreach ($bookings as &$booking) {
                $booking = $this->exchangeWebService->getBooking(
                    $resource,
                    $booking->getId(),
                    $booking->getChangeKey()
                );
            }
            $calendar->setBookings($bookings);
        }

        return $calendar;
    }

    /**
     * Get single booking.
     *
     * @param $resource
     *   The resource to impersonate.
     * @param $id
     *   The Exchange ID.
     * @param $changeKey
     *   The change key for the booking.
     *
     * @return bool|\Itk\ExchangeBundle\Model\ExchangeBooking
     *   Booking information from Exchange.
     */
    public function getBooking($resource, $id, $changeKey)
    {
        return $this->exchangeWebService->getBooking(
            $resource,
            $id,
            $changeKey
        );
    }

    /**
     * Get exchange DSS XML data.
     *
     * @return array
     */
    public function getExchangeDssXmlData()
    {
        return $this->exchangeXMLService->importDssXmlFile();
    }

    /**
     * Get exchange RC XML data.
     *
     * @return array
     */
    public function getExchangeRcXmlData()
    {
        return $this->exchangeXMLService->importRcXmlFile();
    }

    /**
     * Create a new booking.
     *
     * @param \Itk\ExchangeBundle\Entity\Booking $booking
     *   Booking entity to send to Exchange.
     */
    public function createBooking(Booking $booking)
    {
        $this->exchangeMailService->createBooking($booking);
    }

    /**
     * Update an existing booking.
     *
     * @param \Itk\ExchangeBundle\Entity\Booking $booking
     *   Booking entity to send to Exchange.
     */
    public function updateBooking(Booking $booking)
    {
        $this->exchangeMailService->updateBooking($booking);
    }

    /**
     * Cancel a booking.
     *
     * @param \Itk\ExchangeBundle\Entity\Booking $booking
     *   Booking entity to cancel.
     */
    public function cancelBooking(Booking $booking)
    {
        $this->exchangeMailService->cancelBooking($booking);
    }

    /**
     * Check if booking is accepted by Exchange.
     *
     * @param \Itk\ExchangeBundle\Entity\Booking $booking
     *
     * @throw NotFoundHttpException
     *
     * @return bool
     *   TRUE if it's created else FALSE.
     */
    public function isBookingAccepted(Booking $booking)
    {
        if (!$booking->getResource()) {
            throw new NotFoundHttpException('Resource is null');
        }

        // Start by getting the booking from exchange.
        $exchangeCalendar = $this->getResourceBookings(
            $booking->getResource(),
            $booking->getStartTime(),
            $booking->getEndTime()
        );

        // Check that booking exists.
        $exchangeBookings = $exchangeCalendar->getBookings();
        if (!empty($exchangeBookings)) {
            // Check if it's the right booking.
            if ($exchangeBookings[0]->getType(
                ) === ExchangeBooking::TYPE_KOBA && $exchangeBookings[0]->getBody(
                )->getIcalUid() === $booking->getIcalUid()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the ExchangeBookings for a resource in an interval.
     *
     * @param Resource $resource
     *   The resource.
     * @param $startTime
     *   The start time.
     * @param $endTime
     *   The end time.
     *
     * @return array
     *   Array of ExchangeBookings.
     */
    public function getExchangeBookingsForInterval(
        Resource $resource,
        $startTime,
        $endTime
    ) {
        // Start by getting the bookings from exchange.
        $exchangeCalendar = $this->getResourceBookings(
            $resource,
            $startTime,
            $endTime
        );

        return $exchangeCalendar->getBookings();
    }

    /**
     * Does an ExchangeBooking match a Booking?
     *
     * @param ExchangeBooking $exchangeBooking
     *   The exchange booking.
     * @param Booking $booking
     *   The booking.
     *
     * @return bool
     *   Whether or not the $exchangeBooking matches the $booking
     */
    public function doBookingsMatch(
        ExchangeBooking $exchangeBooking,
        Booking $booking
    ) {
        // The bookings match if:
        // 1. The IcalUids match
        // OR
        // 2. The subject and client booking ids match.
        return
            $exchangeBooking->getType() === ExchangeBooking::TYPE_KOBA &&
            (
                $exchangeBooking->getBody()->getIcalUid() === $booking->getIcalUid() ||
                ($exchangeBooking->getSubject() == $booking->getSubject() &&
                    $exchangeBooking->getBody()->getClientBookingId() == $booking->getClientBookingId()
                )
            );
    }
}
