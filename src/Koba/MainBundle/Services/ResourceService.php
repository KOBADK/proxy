<?php
/**
 * @file
 * This contains the Koba resource service.
 */

namespace Koba\MainBundle\Services;

use Koba\MainBundle\Entity\Resource;
use Koba\MainBundle\EntityRepositories\ResourceRepository;
use Itk\ApiBundle\Services\ExchangeService;

/**
 * Class ResourceService
 *
 * @package Koba\MainBundle\Services
 */
class ResourceService {
  protected $resourceRepository;
  protected $helperService;
  protected $exchangeService;

  /**
   * Constructor
   *
   * @param ResourceRepository $resourceRepository
   *   The resource repository.
   * @param ExchangeService $exchangeService
   *   The exchange service.
   */
  function __construct(ResourceRepository $resourceRepository, ExchangeService $exchangeService) {
    $this->resourceRepository = $resourceRepository;
    $this->exchangeService = $exchangeService;
  }

  /**
   * Get all resources
   *
   * @return array
   *   Array of resources.
   */
  public function getAllResources() {
    return $this->resourceRepository->findAll();
  }

  /**
   * Get resource by id
   *
   * @param integer $id
   *   Id of the resource
   * @return Resource
   *   The resource.
   */
  public function getResource($id) {
    $resource = $this->resourceRepository->findOneById($id);

    if (!$resource) {
      // TODO: Throw exception.
    }

    return $resource;
  }

  /**
   * Create a resource
   *
   * @TODO: Implement this!
   *
   * @param \Koba\MainBundle\Entity\Resource $resource
   *   The resource to create.
   *
   * @return boolean
   *   Success.
   */
  public function createResource(Resource $resource) {
    /*
    $validation = $this->helperService->validateResource($resource);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    if ($this->resourcesRepository->findOneByMail($resource->getMail())) {
      return $this->helperService->generateResponse(409, null, array('message' => 'resource already exists'));
    }

    $result = $this->exchangeService->getResource($resource->getMail());

    return $result;
    */
    return null;
  }
}
