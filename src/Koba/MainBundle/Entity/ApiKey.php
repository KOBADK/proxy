<?php
/**
 * @file
 * Contains Apikey entity.
 */

namespace Koba\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * ApiKey.
 *
 * @ORM\Entity(repositoryClass="Koba\MainBundle\Entity\ApiKeyRepository")
 * @ORM\Table(name="koba_apikey")
 */
class ApiKey {
  /**
   * ApiKey
   *
   * @ORM\Column(name="api_key", type="string", nullable=false)
   * @ORM\Id
   *
   * @JMS\Groups("admin")
   */
  protected $apiKey;

  /**
   * @ORM\Column(type="string", nullable=false)
   *
   * @JMS\Groups("admin")
   */
  protected $name;

  /**
   * @ORM\Column(type="json_array", nullable=false)
   *
   * @JMS\Groups("admin")
   */
  protected $configuration;

  /**
   * @ORM\Column(type="string", nullable=true)
   *
   * @JMS\Groups("admin")
   */
  protected $callback;

  /**
   * Get callback.
   *
   * @return string
   */
  public function getCallback() {
    return $this->callback;
  }

  /**
   * Set callback
   *
   * @param string $callback
   */
  public function setCallback($callback) {
    $this->callback = $callback;
  }

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
   * Set name
   *
   * @param string $name
   * @return ApiKey
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName() {
    return $this->name;
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
