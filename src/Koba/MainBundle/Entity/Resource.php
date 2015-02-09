<?php

namespace Koba\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\XmlRoot;

/**
 * A resource. Is hooked up with a mail in Exchange.
 *
 * @ORM\Entity(repositoryClass="Koba\MainBundle\EntityRepositories\ResourceRepository")
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
   * @Groups({"booking_create", "resource"})
   */
  protected $id;

  /**
   * Resource name
   *
   * @ORM\Column(type="string")
   *
   * @Groups({"resource"})
   */
  protected $name;

  /**
   * Resource mail
   *
   * @ORM\Column(type="string")
   *
   * @Groups({"resource_create", "resource"})
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
   * groups that have access to this resource
   *
   * @ORM\ManyToMany(targetEntity="group", inversedBy="resources")
   * @ORM\JoinTable(name="koba_groups_resources")
   **/
  protected $groups;

  /**
   * Bookings of the resource
   *
   * @ORM\OneToMany(targetEntity="Booking", mappedBy="resource")
   **/
  protected $bookings;

  /**
   * Constructor
   */
  public function __construct() {
    $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
   *
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
   *
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
   *
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
   *
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
   *
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
   * Add group
   *
   * @param \Koba\MainBundle\Entity\Group $group
   *
   * @return Resource
   */
  public function addgroup(\Koba\MainBundle\Entity\Group $group) {
    $this->groups[] = $group;

    return $this;
  }

  /**
   * Remove group
   *
   * @param \Koba\MainBundle\Entity\Group $group
   */
  public function removegroup(\Koba\MainBundle\Entity\Group $group) {
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
   * Add bookings
   *
   * @param \Koba\MainBundle\Entity\Booking $bookings
   *
   * @return Resource
   */
  public function addBooking(\Koba\MainBundle\Entity\Booking $bookings) {
    $this->bookings[] = $bookings;

    return $this;
  }

  /**
   * Remove bookings
   *
   * @param \Koba\MainBundle\Entity\Booking $bookings
   */
  public function removeBooking(\Koba\MainBundle\Entity\Booking $bookings) {
    $this->bookings->removeElement($bookings);
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
