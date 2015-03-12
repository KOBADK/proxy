<?php

namespace Itk\ExchangeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * A resource. Is hooked up with a mail in Exchange.
 *
 * @ORM\Entity(repositoryClass="Itk\ExchangeBundle\Entity\ResourceRepository")
 * @ORM\Table(name="koba_resource")
 */
class Resource {
  /**
   * Internal resource ID
   *
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * Resource name
   *
   * @ORM\Column(type="string")
   */
  protected $name;

  /**
   * Resource mail
   *
   * @ORM\Column(type="string")
   */
  protected $mail;

  /**
   * Routing protocol
   *
   * @ORM\Column(type="string")
   */
  protected $routing;

  /**
   * When should the resource be reloaded?
   *
   * @ORM\Column(type="integer")
   */
  protected $expire;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
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

  /**
   * Set routing
   *
   * @param string $routing
   *
   * @return Resource
   */
  public function setRouting($routing) {
    $this->routing = $routing;

    return $this;
  }

  /**
   * Get routing
   *
   * @return string
   */
  public function getRouting() {
    return $this->routing;
  }

  /**
   * Set expire
   *
   * @param integer $expire
   *
   * @return Resource
   */
  public function setExpire($expire) {
    $this->expire = $expire;

    return $this;
  }

  /**
   * Get expire
   *
   * @return integer
   */
  public function getExpire() {
    return $this->expire;
  }
}
