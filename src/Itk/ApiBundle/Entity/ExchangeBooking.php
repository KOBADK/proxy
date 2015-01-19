<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\XmlRoot;

use Symfony\Component\Validator\Constraints AS Assert;

/**
 * A container for an Exchange booking. Used for validation and history.
 *
 * @ORM\Entity
 * @ORM\Table(name="koba_exchange_booking")
 * @XmlRoot("exchange_booking")
 */
class ExchangeBooking {
  /**
   * Internal booking ID
   *
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * Constructor
   */
  public function __construct() {
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }
}
