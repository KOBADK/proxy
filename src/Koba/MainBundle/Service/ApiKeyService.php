<?php
/**
 * @file
 * Contains the ApiKeyService.
 */

namespace Koba\MainBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Koba\MainBundle\Entity\ApiKey;

/**
 * Class ApiKeyService
 *
 * @package Koba\MainBundle
 */
class ApiKeyService
{
    protected $apiKeyRepository;

    /**
     * Constructor.
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->apiKeyRepository = $entityManager->getRepository(ApiKey::class);
    }

    /**
     * Get and validate the apikey.
     *
     * @param string $apiKey
     *   The api key string.
     *
     * @return mixed|ApiKey
     *   The found ApiKey.
     * @throws AccessDeniedException
     */
    public function getApiKey($apiKey)
    {
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
     * @return array
     *   The configuration for the given <apiKey, groupid, resource> tuple.
     */
    public function getResourceConfiguration($apiKey, $groupId, $resourceMail)
    {
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
