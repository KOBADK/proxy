<?php
/**
 * @file
 * This contains the Koba resource service.
 */

namespace Koba\MainBundle\Services;

use Koba\MainBundle\Entity\Resource;
use Koba\MainBundle\Entity\ResourceRepository;
use Itk\ExchangeBundle\Services\ExchangeService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Intl\Exception\NotImplementedException;

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
      throw new NotFoundHttpException('Resource not found.');
    }

    return $resource;
  }

  /**
   * Create a resource
   *
   * @param \Koba\MainBundle\Entity\Resource $resource
   *   The resource to create.
   *
   * @return boolean
   *   Success.
   *
   * @TODO: Implement this!
   */
  public function createResource(Resource $resource) {
    throw new NotImplementedException('not implemented');

    /*
    $validation = $this->helperService->validateResource($resource);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    if ($this->resourceRepository->findOneByMail($resource->getMail())) {
      //return $this->helperService->generateResponse(409, null, array('message' => 'resource already exists'));
    }

    $result = $this->exchangeService->getResource($resource->getMail());

    return $result;
    */
  }

  /**
   * Delete resource with id.
   *
   * @param integer $id
   *   Id of the resource to delete.
   *
   * @TODO: Implement this!
   */
  public function deleteResource($id) {
    throw new NotImplementedException('not implemented');
  }

}
