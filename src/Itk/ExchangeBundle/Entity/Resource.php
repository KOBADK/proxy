<?php

namespace Itk\ExchangeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A resource. Is hooked up with a mail in Exchange.
 *
 * @ORM\Table(name="exchange_resource")
 * @ORM\Entity(repositoryClass="Itk\ExchangeBundle\Entity\ResourceRepository")
 */
class Resource {
  /**
   * Resource name
   *
   * @ORM\Column(type="string")
   * @ORM\Id
   */
  protected $name;

  /**
   * Resource mail
   *
   * @ORM\Column(type="string")
   */
  protected $mail;

  /**
   * Constructor.
   *
   * @param string|null $mail
   *   Resource mail address.
   * @param string|null $name
   *   Resource name.
   */
  public function __construct($mail = NULL, $name = NULL) {
    $this->mail = $mail;
    $this->name = $name;
  }

  /**
   * Set name
   *
   * @param string $name
   *
   * @return Resource
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
   * Set mail
   *
   * @param string $mail
   *
   * @return Resource
   */
  public function setMail($mail) {
    $this->mail = $mail;

    return $this;
  }

  /**
   * Get mail
   *
   * @return string
   */
  public function getMail() {
    return $this->mail;
  }
}
