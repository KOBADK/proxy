<?php

namespace Itk\ExchangeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * A booking. The internal representation of a booking.
 *)
 * @ORM\Table(name="koba_booking")
 */
class Booking {
  CONST DEFAULT_DATAFORMAT = 'Ymd\THis\Z';


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
   * Resource that is booked
   *
   * @ORM\ManyToOne(targetEntity="Resource", inversedBy="bookings")
   * @ORM\JoinColumn(name="resource_id", referencedColumnName="id")
   *
   * @Assert\NotNull
   */
  protected $resource;

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
   * @param string $format
   *   The date format to apply to the date. Defaults to 'Ymd\THis\Z'.
   *
   * @return integer
   */
  public function getStartTime($format = self::DEFAULT_DATAFORMAT) {
    return date($format, $this->startTime);
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
   * @param string $format
   *   The date format to apply to the date. Defaults to 'Ymd\THis\Z'.
   *
   * @return integer
   */
  public function getEndTime($format = self::DEFAULT_DATAFORMAT) {
    return date($format, $this->endTime);
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
   * Set resource
   *
   * @param \Itk\ExchangeBundle\Entity\Resource $resource
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
   * @return \Itk\ExchangeBundle\Entity\Resource
   */
  public function getResource() {
    return $this->resource;
  }
}
