<?php

namespace Itk\ExchangeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * A resource. Is hooked up with a mail in Exchange.
 *
 * @ORM\Table(name="exchange_resource")
 * @ORM\Entity(repositoryClass="Itk\ExchangeBundle\Repository\ResourceRepository")
 */
class Resource
{
    /**
     * Resource mail
     *
     * @ORM\Column(name="mail", type="string")
     * @ORM\Id
     *
     * @JMS\Groups("admin")
     */
    protected $mail;

    /**
     * Resource name
     *
     * @ORM\Column(name="name", type="string")
     *
     * @JMS\Groups("admin")
     */
    protected $name;

    /**
     * Bookings of the resource
     *
     * @ORM\OneToMany(targetEntity="Booking", mappedBy="resource")
     **/
    protected $bookings;

    /**
     * Alias
     *
     * @ORM\Column(name="alias", type="string")
     *
     * @JMS\Groups("admin")
     */
    protected $alias;

    /**
     * Constructor.
     *
     * @param string|null $mail
     *   Resource mail address.
     * @param string|null $name
     *   Resource name.
     * @param string|null $alias
     *   Resource alias.
     */
    public function __construct($mail = null, $name = null, $alias = '')
    {
        $this->mail = $mail;
        $this->name = $name;
        $this->alias = $alias;
        $this->bookings = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return $this
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string|null
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Add bookings
     *
     * @param \Itk\ExchangeBundle\Entity\Booking $booking
     *
     * @return $this
     */
    public function addBooking(\Itk\ExchangeBundle\Entity\Booking $booking)
    {
        $this->bookings[] = $booking;

        return $this;
    }

    /**
     * Remove bookings
     *
     * @param \Itk\ExchangeBundle\Entity\Booking $booking
     *
     * @return $this
     */
    public function removeBooking(\Itk\ExchangeBundle\Entity\Booking $booking)
    {
        $this->bookings->removeElement($booking);

        return $this;
    }

    /**
     * Get bookings
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getBookings()
    {
        return $this->bookings;
    }

    /**
     * Set alias
     *
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string|null
     */
    public function getAlias()
    {
        return $this->alias;
    }
}
