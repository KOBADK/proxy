<?php

namespace Itk\ExchangeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * A booking. The internal representation of a booking.
 *
 * @ORM\Table(name="exchange_booking")
 * @ORM\Entity()
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
   * @ORM\Column(type="string", nullable=true)
   */
  protected $exchangeId;

  /**
   * ICal event UID
   *
   * @ORM\Column(type="string", nullable=true)
   */
  protected $icalUid;

  /**
   * Resource that is booked
   *
   * @ORM\ManyToOne(targetEntity="Resource", inversedBy="bookings")
   * @ORM\JoinColumn(name="resource", referencedColumnName="mail")
   *
   * @Assert\NotNull
   */
  protected $resource;

  /**
   * Phone number.
   *
   * @ORM\Column(name="phone", type="string", nullable=true)
   */
  protected $phone;

  /**
   * Api key.
   *
   * @ORM\Column(name="api_key", type="string", nullable=true)
   */
  protected $apiKey;

  /**
   * Start time
   *
   * @ORM\Column(name="start_time", type="integer")
   *
   * @Assert\NotBlank
   */
  protected $startTime;

  /**
   * End time
   *
   * @ORM\Column(name="end_time", type="integer")
   *
   * @Assert\NotBlank
   */
  protected $endTime;

  /**
   * Subject
   *
   * @ORM\Column(name="subject", type="string")
   *
   * @Assert\NotBlank
   */
  protected $subject;

  /**
   * Description
   *
   * @ORM\Column(name="description", type="text")
   *
   * @Assert\NotBlank
   */
  protected $description;

  /**
   * @ORM\Column(name="name", type="text")
   *
   * @Assert\NotBlank
   */
  protected $name;

  /**
   * @ORM\Column(name="mail", type="text")
   *
   * @Assert\NotBlank
   */
  protected $mail;

  /**
   * @ORM\Column(name="status", type="text")
   *
   * @TODO: Find a better way to set status, using bit-string or enmu in the
   *        database.
   *
   * @Assert\NotBlank
   */
  protected $status;


  /**
   * Get the apiKey
   *
   * @return string
   */
  public function getApiKey() {
    return $this->apiKey;
  }

  /**
   * Set the apiKey.
   *
   * @param string $apiKey
   */
  public function setApiKey($apiKey) {
    $this->apiKey = $apiKey;
  }

  /**
   * Get phone.
   *
   * @return string
   */
  public function getPhone() {
    return $this->phone;
  }

  /**
   * Set the phone.
   *
   * @param string $phone
   */
  public function setPhone($phone) {
    $this->phone = $phone;
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
   * Set exchangeId
   *
   * @param string $exchangeId
   *
   * @return Booking
   */
  public function setExchangeId($exchangeId) {
    $this->exchangeId = $exchangeId;

    return $this;
  }

  /**
   * Get exchangeId
   *
   * @return string
   */
  public function getExchangeId() {
    return $this->exchangeId;
  }

  /**
   * Set icalUid
   *
   * @param string $icalUid
   *
   * @return Booking
   */
  public function setIcalUid($icalUid) {
    $this->icalUid = $icalUid;

    return $this;
  }

  /**
   * Get icalUid
   *
   * @return string
   */
  public function getIcalUid() {
    return $this->icalUid;
  }


  /**
   * Set startTime
   *
   * @param integer $startTime
   *
   * @return Booking
   */
  public function setStartTime($startTime) {
    $this->startTime = $startTime;

    return $this;
  }

  /**
   * Get startTime
   *
   * @return integer
   */
  public function getStartTime() {
    return $this->startTime;
  }

  /**
   * Set endTime
   *
   * @param integer $endTime
   *
   * @return Booking
   */
  public function setEndTime($endTime) {
    $this->endTime = $endTime;

    return $this;
  }

  /**
   * Get endTime
   *
   * @return integer
   */
  public function getEndTime() {
    return $this->endTime;
  }

  /**
   * Set subject
   *
   * @param string $subject
   *
   * @return Booking
   */
  public function setSubject($subject) {
    $this->subject = $subject;

    return $this;
  }

  /**
   * Get subject
   *
   * @return string
   */
  public function getSubject() {
    return $this->subject;
  }

  /**
   * Set description
   *
   * @param string $description
   *
   * @return Booking
   */
  public function setDescription($description) {
    $this->description = $description;

    return $this;
  }

  /**
   * Get description
   *
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Set name
   *
   * @param string $name
   *
   * @return Booking
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
   * @return Booking
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
   * Set status to pending
   *
   * @return Booking
   */
  public function setStatusPending() {
    $this->status = 'PENDING';

    return $this;
  }

  /**
   * Set status to pending
   *
   * @return Booking
   */
  public function setStatusDenied() {
    $this->status = 'DENIED';

    return $this;
  }

  /**
   * Set status to pending
   *
   * @return Booking
   */
  public function setStatusAccepted() {
    $this->status = 'ACCEPTED';

    return $this;
  }

  /**
   * Set status to pending
   *
   * @return Booking
   */
  public function setStatusCanceled() {
    $this->status = 'CANCELED';

    return $this;
  }

  /**
   * Get status
   *
   * @return string
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Set resource
   *
   * @param null|\Itk\ExchangeBundle\Entity\Resource $resource
   *
   * @return Booking
   */
  public function setResource(\Itk\ExchangeBundle\Entity\Resource $resource = NULL) {
    $this->resource = $resource;

    return $this;
  }

  /**
   * Get resource
   *
   * @return \Itk\ExchangeBundle\Entity\Resource|null
   */
  public function getResource() {
    return $this->resource;
  }
}
