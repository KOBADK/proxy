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

/**
 * Class ResourcesService
 *
 * @package Itk\ApiBundle\Services
 */
class ResourcesService {
  protected $container;
  protected $doctrine;
  protected $em;
  protected $bookingRepository;
  protected $helperService;

  /**
   * Constructor
   *
   * @param Container $container
   * @param HelperService $helperService
   */
  function __construct(Container $container, HelperService $helperService) {
    $this->container = $container;
    $this->helperService = $helperService;
    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();
    $this->resourceRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Resource');
  }

  /**
   * Get all resources
   *
   * @return array
   */
  public function getAllResources() {
    $resources = $this->resourceRepository->findAll();

    return $this->helperService->generateResponse(200, $resources);
  }
}