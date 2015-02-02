<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\XmlRoot;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * A user. Is hooked up with a mail in the Exchange system.
 *
 * @ORM\Entity
 * @ORM\Table(name="koba_user")
 * @XmlRoot("user")
 */
class User implements UserInterface, \Serializable {
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
   * User unique id
   *
   * @ORM\Column(type="string", nullable=false)
   *
   * @Assert\NotBlank
   *
   * @Groups({"user"})
   */
  protected $uniqueId;

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
   * Set unique id
   *
   * @param string $uniqueId
   *
   * @return User
   */
  public function setUniqueId($uniqueId) {
    $this->uniqueId = $uniqueId;

    return $this;
  }

  /**
   * Get uniqueId
   *
   * @return string
   */
  public function getUniqueId() {
    return $this->uniqueId;
  }

  /**
   * Set name
   *
   * @param string $name
   *
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
   *
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
   *
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
   *
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
   * @return array
   */
  public function getRoles() {
    $arr = array();
    foreach ($this->getFullRoles() as $role) {
      $arr[] = $role->getRole();
    }
    return $arr;
  }

  /**
   * Get roles
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getFullRoles() {
    return $this->roles;
  }

  /**
   * Add booking
   *
   * @param \Itk\ApiBundle\Entity\Booking $booking
   *
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

  /**
   * Returns the password used to authenticate the user.
   *
   * This should be the encoded password. On authentication, a plain-text
   * password will be salted, encoded, and then compared to this value.
   *
   * @return string The password
   */
  public function getPassword() {
    // TODO: Implement getPassword() method.
    return NULL;
  }

  /**
   * Returns the salt that was originally used to encode the password.
   *
   * This can return null if the password was not encoded using a salt.
   *
   * @return string|null The salt
   */
  public function getSalt() {
    // TODO: Implement getSalt() method.
    return NULL;
  }

  /**
   * Returns the username used to authenticate the user.
   *
   * @return string The username
   */
  public function getUsername() {
    return $this->getId();
  }

  /**
   * Removes sensitive data from the user.
   *
   * This is important if, at any given point, sensitive information like
   * the plain-text password is stored on this object.
   */
  public function eraseCredentials() {
    // TODO: Implement eraseCredentials() method.
  }

  /**
   * @see \Serializable::serialize()
   */
  public function serialize() {
    return serialize(array(
      $this->id
    ));
  }

  /**
   * @see \Serializable::unserialize()
   */
  public function unserialize($serialized) {
    list (
      $this->id,
      ) = unserialize($serialized);
  }
}
