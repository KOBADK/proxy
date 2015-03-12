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
   * @ORM\Column(type="string")
   * @ORM\Id
   */
  protected $apiKey;
}
