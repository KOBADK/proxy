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
   * Get and validate the apikey.
   *
   * @throws AccessDeniedException
   *
   * @param string $apiKey
   *   The api key string.
   *
   * @return ApiKey
   *   The found ApiKey.
   */
  public function getApiKey($apiKey) {
    if (!isset($apiKey)) {
      throw new AccessDeniedException();
    }

    $apiKey = $this->apiKeyRepository->findOneByApiKey($apiKey);

    if (!$apiKey) {
      throw new AccessDeniedException();
    }

    return $apiKey;
  }

  /**
   * Check access for an api key with a given groupId for access to a given resource.
   * Return .
   *
   * @throws AccessDeniedException
   *
   * @param $apiKey
   *   The api key.
   * @param $groupId
   *   The group id.
   * @param $resourceMail
   *   The mail of the resource.
   *
   * @returns array
   *   The configuration for the given <apiKey, groupid, resource> tuple.
   */
  public function getResourceConfiguration($apiKey, $groupId, $resourceMail) {
    $configuration = $apiKey->getConfiguration();
    if (isset($configuration['groups'])) {
      foreach ($configuration['groups'] as $group) {
        if ($group['id'] === $groupId) {
          foreach ($group['resources'] as $resourceConfiguration) {
            if ($resourceConfiguration['mail'] === $resourceMail) {
              // Access granted.
              return $resourceConfiguration;
            }
          }
        }
      }
    }

    // Access not granted to these credentials.
    throw new AccessDeniedException();
  }
}
