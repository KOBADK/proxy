<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * @ORM\Entity
 * @ORM\Table(name="koba_resource")
 * @XmlRoot("resource")
 */
class Resource {
  /**
   * Internal resource ID
   *
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   *
   * @Groups({"booking_create"})
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
   *
   * @Groups({"resource_create"})
   */
  protected $mail;

  /**
   * Routing protocol
   *
   * @ORM\Column(type="string")
   */
  protected $routing;

  /**
   * Mailbox type
   *
   * @ORM\Column(type="string")
   */
  protected $mailbox;

  /**
   * When should the resource be reloaded?
   *
   * @ORM\Column(type="integer")
   */
  protected $expire;

  /**
   * Roles that have access to this resource
   *
   * @ORM\ManyToMany(targetEntity="Role", inversedBy="resources")
   * @ORM\JoinTable(name="koba_roles_resources")
   **/
  protected $roles;

  /**
   * Bookings of the resource
   *
   * @ORM\ManyToMany(targetEntity="Booking", mappedBy="resources")
   **/
  protected $bookings;

  /**
   * Constructor
   */
  public function __construct() {
    $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set name
   *
   * @param string $name
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
   * Set mailbox
   *
   * @param string $mailbox
   * @return Resource
   */
  public function setMailbox($mailbox) {
    $this->mailbox = $mailbox;

    return $this;
  }

  /**
   * Get mailbox
   *
   * @return string
   */
  public function getMailbox() {
    return $this->mailbox;
  }

  /**
   * Set expire
   *
   * @param integer $expire
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

  /**
   * Add role
   *
   * @param \Itk\ApiBundle\Entity\Role $role
   * @return Resource
   */
  public function addRole(\Itk\ApiBundle\Entity\Role $role) {
    $this->roles[] = $role;

    return $this;
  }

  /**
   * Remove role
   *
   * @param \Itk\ApiBundle\Entity\Role $role
   */
  public function removeRole(\Itk\ApiBundle\Entity\Role $role) {
    $this->roles->removeElement($role);
  }

  /**
   * Get roles
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getRoles() {
    return $this->roles;
  }

  /**
   * Add booking
   *
   * @param \Itk\ApiBundle\Entity\Booking $booking
   * @return Resource
   */
  public function addBooking(\Itk\ApiBundle\Entity\Booking $booking) {
    $this->bookings[] = $booking;

    return $this;
  }

  /**
   * Remove booking
   *
   * @param \Itk\ApiBundle\Entity\Booking $booking
   */
  public function removeBooking(\Itk\ApiBundle\Entity\Booking $booking) {
    $this->bookings->removeElement($booking);
  }

  /**
   * Get bookings
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getBookings() {
    return $this->bookings;
  }
}
