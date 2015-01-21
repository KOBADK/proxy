<?php
/**
 * @file
 * This file is a part of the Itk ApiBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Itk\ApiBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Itk\ApiBundle\Entity\Resource;

/**
 * Class ResourcesService
 *
 * @package Itk\ApiBundle\Services
 */
class ResourcesService {
  protected $container;
  protected $doctrine;
  protected $em;
  protected $resourcesRepository;
  protected $helperService;
  protected $exchangeService;

  /**
   * Constructor
   *
   * @param Container $container
   * @param HelperService $helperService
   * @param ExchangeService $exchangeService
   */
  function __construct(Container $container, HelperService $helperService, ExchangeService $exchangeService) {
    $this->container = $container;
    $this->helperService = $helperService;
    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();
    $this->resourcesRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Resource');
    $this->exchangeService = $exchangeService;
  }

  /**
   * Get all resources
   *
   * @return array
   */
  public function getAllResources() {
    $resources = $this->resourcesRepository->findAll();

    return $this->helperService->generateResponse(200, $resources);
  }

  /**
   * Get resource by id
   *
   * @param integer $id id of the resource
   * @return array
   */
  public function getResource($id) {
    $resource = $this->resourcesRepository->findOneById($id);

    if (!$resource) {
      return $this->helperService->generateResponse(404, null, array('message' => 'resource not found'));
    }

    return $this->helperService->generateResponse(200, $resource);
  }

  /**
   * Create a resource
   *
   * @param \Itk\ApiBundle\Entity\Resource $resource resource to create
   * @return array
   */
  public function createResource(Resource $resource) {
    $validation = $this->helperService->validateResource($resource);
    if ($validation['status'] !== 200) {
      return $this->helperService->generateResponse($validation['status'], null, $validation['errors']);
    }

    if ($this->resourcesRepository->findOneByMail($resource->getMail())) {
      return $this->helperService->generateResponse(409, null, array('message' => 'resource already exists'));
    }

    // TODO: Validate resource against Exchange
    $result = $this->exchangeService->getResource($resource->getMail());

    return $result;
  }
}