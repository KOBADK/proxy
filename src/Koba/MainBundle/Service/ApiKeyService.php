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

  /**
   * Check access for an api key with a given groupId for access to a given resource.
   *
   * @param $apiKey
   * @param $groupId
   * @param $resourceMail
   */
  public function checkAccess($apiKey, $groupId, $resourceMail) {
    $configuration = $apiKey->getConfiguration();
    if (isset($configuration['groups'])) {
      foreach ($configuration['groups'] as $group) {
        if ($group['id'] === $groupId) {
          foreach ($group['resources'] as $resource) {
            if ($resource['mail'] === $resourceMail) {
              return;
            }
          }
        }
      }
    }

    throw new AccessDeniedException();
  }
}
