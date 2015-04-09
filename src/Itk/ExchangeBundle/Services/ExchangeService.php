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
   * @param string $resourceId
   *   The mail of the resource.
   * @param integer $interestPeriod
   *   Seconds of interestPeriod.
   *
   * @return array
   */
  public function getBookingsForResource($resourceId, $interestPeriod) {
    $now = mktime(0, 0, 0);
    return $this->exchangeWebService->getRessourceBookings($resourceId, $now, $now + $interestPeriod);
  }

  /**
   * Create a booking.
   *
   * @param Booking $booking
   *   The booking to create.
   */
  public function createBooking(Booking $booking) {
    $this->exchangeMailService->createBooking($booking);
  }

  /**
   * Get exchange XML data.
   *
   * @return array
   */
  public function getExchangeXMLData() {
    return $this->exchangeXMLService->importXmlFile();
  }
}
