<?php

namespace Koba\MainBundle\Entity;

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
}
