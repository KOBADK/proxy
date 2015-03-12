<?php
/**
 * @file
 * Contains Apikey entity.
 */

namespace Koba\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * ApiKey.
 *
 * @ORM\Entity(repositoryClass="Koba\MainBundle\Entity\ApikeyRepository")
 * @ORM\Table(name="koba_apikey")
 */
class ApiKey {
  /**
   * ApiKey
   *
   * @ORM\Column(type="string", nullable=false)
   * @ORM\Id
   */
  protected $apiKey;

  /**
   * @ORM\Column(type="json_array", nullable=false)
   */
  protected $configuration;

  /**
   * Set apiKey
   *
   * @param string $apiKey
   * @return ApiKey
   */
  public function setApiKey($apiKey) {
    $this->apiKey = $apiKey;

    return $this;
  }

  /**
   * Get apiKey
   *
   * @return string
   */
  public function getApiKey() {
    return $this->apiKey;
  }

  /**
   * Set configuration
   *
   * @param array $configuration
   * @return ApiKey
   */
  public function setConfiguration($configuration) {
    $this->configuration = $configuration;

    return $this;
  }

  /**
   * Get configuration
   *
   * @return array
   */
  public function getConfiguration() {
    return $this->configuration;
  }
}
