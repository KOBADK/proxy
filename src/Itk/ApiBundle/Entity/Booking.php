<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\XmlRoot;

use Symfony\Component\Validator\Constraints AS Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="koba_booking")
 * @XmlRoot("booking")
 */
class Booking {
  /**
   * Internal booking ID
   *
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   *
   * @Assert\NotNull
   */
  protected $id;

  /**
   * Exchange event ID
   *
   * @ORM\Column(type="string")
   */
  protected $eid;

  /**
   * User that owns the booking
   *
   * @ORM\ManyToOne(targetEntity="User", inversedBy="bookings")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   **/
  protected $user;

  /**
   * Resource that is booked
   *
   * @ORM\ManyToOne(targetEntity="Resource", inversedBy="bookings")
   * @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
   */
  protected $resource;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Set eid
   *
   * @param string $eid
   * @return Booking
   */
  public function setEid($eid) {
    $this->eid = $eid;

    return $this;
  }

  /**
   * Get eid
   *
   * @return string
   */
  public function getEid() {
    return $this->eid;
  }

  /**
   * Set user
   *
   * @param \Itk\ApiBundle\Entity\User $user
   * @return Booking
   */
  public function setUser(\Itk\ApiBundle\Entity\User $user = null) {
    $this->user = $user;

    return $this;
  }

  /**
   * Get user
   *
   * @return \Itk\ApiBundle\Entity\User
   */
  public function getUser() {
    return $this->user;
  }
}
