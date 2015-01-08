<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="koba_booking")
 */
class Booking {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="string")
   */
  protected $eid;

  /**
   * @ORM\ManyToOne(targetEntity="User", inversedBy="bookings")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   **/
  protected $user;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set eid
   *
   * @param string $eid
   * @return Booking
   */
  public function setEid($eid)
  {
    $this->eid = $eid;

    return $this;
  }

  /**
   * Get eid
   *
   * @return string
   */
  public function getEid()
  {
    return $this->eid;
  }

  /**
   * Set user
   *
   * @param \Itk\ApiBundle\Entity\User $user
   * @return Booking
   */
  public function setUser(\Itk\ApiBundle\Entity\User $user = null)
  {
    $this->user = $user;

    return $this;
  }

  /**
   * Get user
   *
   * @return \Itk\ApiBundle\Entity\User
   */
  public function getUser()
  {
    return $this->user;
  }
}
