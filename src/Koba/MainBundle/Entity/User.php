<?php

namespace Koba\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\XmlRoot;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints AS Assert;

/**
 * A user. Is hooked up with a mail in the Exchange system.
 *
 * @ORM\Entity(repositoryClass="Koba\MainBundle\EntityRepositories\UserRepository")
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
   * @Groups({"user", "group"})
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
   * The user's groups
   *
   * @ORM\ManyToMany(targetEntity="group", inversedBy="users")
   * @ORM\JoinTable(name="koba_groups_users")
   *
   * @Assert\Collection
   **/
  protected $groups;

  /**
   * Name
   *
   * @ORM\Column(type="string", nullable=false)
   *
   * @Assert\NotBlank
   *
   * @Groups({"user", "group"})
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
    $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Add group
   *
   * @param \Koba\MainBundle\Entity\Group $group
   *
   * @return User
   */
  public function addGroup(\Koba\MainBundle\Entity\Group $group) {
    $this->groups[] = $group;

    return $this;
  }

  /**
   * Remove group
   *
   * @param \Koba\MainBundle\Entity\Group $group
   */
  public function removeGroup(\Koba\MainBundle\Entity\Group $group) {
    $this->groups->removeElement($group);
  }

  /**
   * Get groups
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getGroups() {
    return $this->groups;
  }

  /**
   * Add booking
   *
   * @param \Koba\MainBundle\Entity\Booking $booking
   *
   * @return User
   */
  public function addBooking(\Koba\MainBundle\Entity\Booking $booking) {
    $this->bookings[] = $booking;

    return $this;
  }

  /**
   * Remove booking
   *
   * @param \Koba\MainBundle\Entity\Booking $booking
   */
  public function removeBooking(\Koba\MainBundle\Entity\Booking $booking) {
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
  }

  /**
   * Get groups
   *
   * @return array
   *
   *
   * @TODO: Extend to return different roles.
   */
  public function getRoles() {
    return array('ROLE_USER');
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
