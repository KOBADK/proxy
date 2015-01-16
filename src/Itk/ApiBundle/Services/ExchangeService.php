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
 * Class ExchangeService
 *
 * @package Itk\ApiBundle\Services
 */
class ExchangeService {
  protected $container;
  protected $doctrine;
  protected $em;
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
  }

  /**
   * Get a resource from exchange
   *
   * @param string $mail the mail that identifies the resource in Exchange
   * @return array
   */
  public function getResource($mail) {
    return $this->helperService->generateResponse(500, null, array('message' => 'not implemented'));
  }
}