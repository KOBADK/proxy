<?php
/**
 * @file
 * Contains the ApiKeyService.
 */

namespace Koba\MainBundle\Service;

use Koba\MainBundle\Entity\ApiKeyRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Koba\MainBundle\Entity\ApiKey;

/**
 * Class ApiKeyService
 *
 * @package Koba\MainBundle
 */
class ApiKeyService {
  protected $apiKeyRepository;

  /**
   * Constructor.
   *
   * @param ApiKeyRepository $apiKeyRepository
   *   The EntityManager.
   */
  public function __construct(ApiKeyRepository $apiKeyRepository) {
    $this->apiKeyRepository = $apiKeyRepository;
  }

  /**
   * Validate the apikey.
   *
   * @param Request $request
   *   The Request object.
   *
   * @return ApiKey
   *   The found ApiKey.
   */
  public function getApiKey(Request $request) {
    $apiKey = $request->query->get('apikey');

    if (!isset($apiKey)) {
      throw new AccessDeniedException();
    }

    $apiKey = $this->apiKeyRepository->findOneByApiKey($apiKey);

    if (!$apiKey) {
      throw new AccessDeniedException();
    }

    return $apiKey;
  }
}
