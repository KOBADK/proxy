<?php
/**
 * @file
 * @todo Missing file description?
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
    $this->helperService = $helperService;

    // @todo: The service is only dependent on the container to get the entity
    // manager?
    $this->container = $container;

    // @TODO: Inject "EntityManager $em" -> "@doctrine.orm.entity_manager" so
    // it's not dependent on doctrine inside the service.
    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();

    $this->resourcesRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Resource');
    $this->exchangeService = $exchangeService;
  }

  /**
   * Get all resources
   *
   * @return array
   *   @TODO Missing description?
   */
  public function getAllResources() {
    $resources = $this->resourcesRepository->findAll();

    return $this->helperService->generateResponse(200, $resources);
  }

  /**
   * Get resource by id
   *
   * @param integer $id id of the resource
   *   @TODO Missing description?
   * @return array
   *   @TODO Missing description?
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
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
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
