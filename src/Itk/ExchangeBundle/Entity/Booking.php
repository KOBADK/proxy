<?php

namespace Koba\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\XmlRoot;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * A booking. The internal representation of a booking.
 *
 * @ORM\Entity(repositoryClass="Koba\MainBundle\Entity\BookingRepository")
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

}
