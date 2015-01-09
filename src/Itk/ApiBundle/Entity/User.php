<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="koba_user")
 */
class User {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="string")
   */
  protected $uuid;

  /**
   * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
   * @ORM\JoinTable(name="koba_roles_users")
   **/
  protected $roles;

  /**
   * @ORM\Column(type="string")
   */
  protected $name;

  /**
   * @ORM\Column(type="string")
   */
  protected $mail;

  /**
   * @ORM\Column(type="boolean")
   */
  protected $status;

  /**
   * @ORM\OneToMany(targetEntity="Booking", mappedBy="user")
   **/
  protected $bookings;

  /**
   * Constructor
   */
  public function __construct() {
    $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
    $this->bookings = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set uuid
   *
   * @param string $uuid
   * @return User
   */
  public function setUuid($uuid) {
    $this->uuid = $uuid;

    return $this;
  }

  /**
   * Get uuid
   *
   * @return string
   */
  public function getUuid() {
    return $this->uuid;
  }

  /**
   * Set name
   *
   * @param string $name
   * @return User
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
   * @return User
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
   * Set status
   *
   * @param boolean $status
   * @return User
   */
  public function setStatus($status) {
    $this->status = $status;

    return $this;
  }

  /**
   * Get status
   *
   * @return boolean
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Add role
   *
   * @param \Itk\ApiBundle\Entity\Role $role
   * @return User
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
   * @return User
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
