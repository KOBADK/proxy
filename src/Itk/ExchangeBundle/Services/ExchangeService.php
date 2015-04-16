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
use Doctrine\ORM\EntityManager;
use Itk\ExchangeBundle\Entity\Resource;
use Itk\ExchangeBundle\Entity\ResourceRepository;
use Itk\ExchangeBundle\Entity\Booking;
use Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException;

/**
 * Class ExchangeService
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeService {
  protected $exchangeADService;
  protected $resourceRepository;
  protected $entityManager;
  protected $exchangeMailService;
  protected $exchangeXMLService;
  protected $exchangeWebService;

  public function __construct(ExchangeADService $exchangeADService, ResourceRepository $resourceRepository, ExchangeMailService $exchangeMailService, ExchangeXMLService $exchangeXMLService, ExchangeWebService $exchangeWebService) {
    $this->exchangeADService = $exchangeADService;
    $this->resourceRepository = $resourceRepository;
    $this->exchangeMailService = $exchangeMailService;
    $this->exchangeXMLService = $exchangeXMLService;
    $this->exchangeWebService = $exchangeWebService;
  }

  /**
   * Get all resources from Exchange.
   */
  public function getResources() {
    return $this->resourceRepository->findAll();
  }

  /**
   * Refresh the available resource entities.
   */
  public function refreshResources() {
    $resources = $this->exchangeADService->getResources();
    $em = $this->resourceRepository->getEntityManager();

    foreach ($resources as $key => $value) {
      $resource = $this->resourceRepository->findOneByMail($key);

      if (!$resource) {
        $resource = new Resource($key, $value);
        $em->persist($resource);
      }
      else {
        $resource->setName($value);
      }
    }

    $em->flush();
  }

  /**
   * Get bookings for a given resource.
   *
   * @param \Itk\ExchangeBundle\Entity\Resource $resource
   *   The mail of the resource.
   * @param int $from
   *   The the start of the interval as unix timestamp.
   * @param int $to
   *   The the end of the interval as unix timestamp.
   * @param bool $enrich
   *   Enrich the result with information form the bookings body.
   *
   * @return \Itk\ExchangeBundle\Model\ExchangeCalendar
   */
  public function getBookingsForResource(Resource $resource, $from, $to, $enrich = TRUE) {
    // Get basic calendar information.
    $calendar = $this->exchangeWebService->getRessourceBookings($resource, $from, $to);

    /**
     * @TODO: Rename the bookings -> ExchangeBookings to remove mis-use to
     *        booking entity.
     */
    // Check if body information should be included.
    if ($enrich) {
      $bookings = &$calendar->getBookings();
      foreach($bookings as &$booking) {
        $booking = $this->exchangeWebService->getBooking($booking->getId(), $booking->getChangeKey());
      }
    }

    return $calendar;
  }

  /**
   * Get exchange DSS XML data.
   *
   * @return array
   */
  public function getExchangeDssXmlData() {
    return $this->exchangeXMLService->importDssXmlFile();
  }

  /**
   * Get exchange RC XML data.
   *
   * @return array
   */
  public function getExchangeRcXmlData() {
    return $this->exchangeXMLService->importRcXmlFile();
  }

  /**
   * Create a new booking.
   *
   * @param \Itk\ExchangeBundle\Entity\Booking $booking
   *   Booking entity to send to Exchange.
   */
  public function createBooking(Booking $booking) {
    $this->exchangeMailService->createBooking($booking);
  }

  /**
   * Cancel a booking.
   *
   * @param \Itk\ExchangeBundle\Entity\Booking $booking
   *   Booking entity to cancel.
   */
  public function cancelBooking(Booking $booking) {
    $this->exchangeMailService->cancelBooking($booking);
  }

  /**
   * Check if booking is accepted by Exchange.
   *
   * @param \Itk\ExchangeBundle\Entity\Booking $booking
   *
   * @return bool
   *   TRUE if it's created else FALSE.
   */
  public function isBookingAccepted(Booking $booking) {
    throw new ExchangeNotSupportedException();
  }
}
