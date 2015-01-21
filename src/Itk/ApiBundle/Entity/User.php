<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\XmlRoot;

use Symfony\Component\Validator\Constraints AS Assert;

/**
 * A user. Is hooked up with a mail in the Exchange system.
 *
 * @ORM\Entity
 * @ORM\Table(name="koba_user")
 * @XmlRoot("user")
 */
class User {
  /**
   * Internal user ID
   *
   * @ORM\Column(type="integer", nullable=false)
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   *
   * @Assert\NotNull
   *
   * @Groups({"user", "role"})
   */
  protected $id;

  /**
   * User UUID
   *
   * @ORM\Column(type="string", nullable=false)
   *
   * @Assert\NotBlank
   *
   * @Groups({"user"})
   */
  protected $uuid;

  /**
   * The user's roles
   *
   * @ORM\ManyToMany(targetEntity="Role", inversedBy="users")
   * @ORM\JoinTable(name="koba_roles_users")
   *
   * @Assert\Collection
   **/
  protected $roles;

  /**
   * Name
   *
   * @ORM\Column(type="string", nullable=false)
   *
   * @Assert\NotBlank
   *
   * @Groups({"user", "role"})
   */
  protected $name;

  /**
   * Email
   *
   * @ORM\Column(type="string", nullable=false)
   *
   * @Assert\NotBlank
   * @Assert\Email
   *
   * @Groups({"user"})
   */
  protected $mail;

  /**
   * User status (active?)
   *
   * @ORM\Column(type="boolean", nullable=false)
   *
   * @Assert\NotNull
   *
   * @Groups({"userstatus", "user"})
   */
  protected $status;

  /**
   * User's bookings
   *
   * @ORM\OneToMany(targetEntity="Booking", mappedBy="user")
   *
   * @Assert\Collection
   *
   * @Groups({})
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
