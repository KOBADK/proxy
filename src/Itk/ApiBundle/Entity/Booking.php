<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\XmlRoot;

use Symfony\Component\Validator\Constraints AS Assert;

/**
 * A booking. The internal representation of a booking.
 *
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
   */
  protected $id;

  /**
   * Exchange event ID
   *
   * @ORM\Column(type="string")
   *
   * @Assert\NotNull
   */
  protected $eid;

  /**
   * User that owns the booking
   *
   * @ORM\ManyToOne(targetEntity="User", inversedBy="bookings")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
   *
   * @Assert\NotNull
   **/
  protected $user;

  /**
   * Resource that is booked
   *
   * @ORM\ManyToMany(targetEntity="Resource", inversedBy="bookings")
   * @ORM\JoinTable(name="koba_bookings_resources")
   *
   * @Groups({"booking_create"})
   *
   * @Assert\NotNull
   * @Assert\Collection
   */
  protected $resources;

  /**
   * Constructor
   */
  public function __construct() {
    $this->resources = new \Doctrine\Common\Collections\ArrayCollection();
  }

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

  /**
   * Add resources
   *
   * @param \Itk\ApiBundle\Entity\Resource $resources
   * @return Booking
   */
  public function addResource(\Itk\ApiBundle\Entity\Resource $resources) {
    $this->resources[] = $resources;

    return $this;
  }

  /**
   * Remove resources
   *
   * @param \Itk\ApiBundle\Entity\Resource $resources
   */
  public function removeResource(\Itk\ApiBundle\Entity\Resource $resources) {
    $this->resources->removeElement($resources);
  }

  /**
   * Get resources
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getResources() {
    return $this->resources;
  }
}
