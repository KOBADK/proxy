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

/**
 * Class ExchangeService
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeService {
  protected $exchangeADService;
  protected $resourceRepository;
  protected $entityManager;

  public function __construct(ExchangeADService $exchangeADService, ResourceRepository $resourceRepository) {
    $this->exchangeADService = $exchangeADService;
    $this->resourceRepository = $resourceRepository;
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
}
