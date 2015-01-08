<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="koba_resource")
 */
class Resource {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="string")
   */
  protected $name;

  /**
   * @ORM\Column(type="string")
   */
  protected $mail;

  /**
   * @ORM\Column(type="string")
   */
  protected $routing;

  /**
   * @ORM\Column(type="string")
   */
  protected $mailbox;

  /**
   * @ORM\Column(type="integer")
   */
  protected $expire;

  /**
   * @ORM\ManyToMany(targetEntity="Role", inversedBy="resources")
   * @ORM\JoinTable(name="koba_roles_resources")
   **/
  protected $roles;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->roles = new \Doctrine\Common\Collections\ArrayCollection();
  }

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set name
   *
   * @param string $name
   * @return Resource
   */
  public function setName($name)
  {
    $this->name = $name;

    return $this;
  }

  /**
   * Get name
   *
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Set mail
   *
   * @param string $mail
   * @return Resource
   */
  public function setMail($mail)
  {
    $this->mail = $mail;

    return $this;
  }

  /**
   * Get mail
   *
   * @return string
   */
  public function getMail()
  {
    return $this->mail;
  }

  /**
   * Set routing
   *
   * @param string $routing
   * @return Resource
   */
  public function setRouting($routing)
  {
    $this->routing = $routing;

    return $this;
  }

  /**
   * Get routing
   *
   * @return string
   */
  public function getRouting()
  {
    return $this->routing;
  }

  /**
   * Set mailbox
   *
   * @param string $mailbox
   * @return Resource
   */
  public function setMailbox($mailbox)
  {
    $this->mailbox = $mailbox;

    return $this;
  }

  /**
   * Get mailbox
   *
   * @return string
   */
  public function getMailbox()
  {
    return $this->mailbox;
  }

  /**
   * Set expire
   *
   * @param integer $expire
   * @return Resource
   */
  public function setExpire($expire)
  {
    $this->expire = $expire;

    return $this;
  }

  /**
   * Get expire
   *
   * @return integer
   */
  public function getExpire()
  {
    return $this->expire;
  }

  /**
   * Add roles
   *
   * @param \Itk\ApiBundle\Entity\Role $roles
   * @return Resource
   */
  public function addRole(\Itk\ApiBundle\Entity\Role $roles)
  {
    $this->roles[] = $roles;

    return $this;
  }

  /**
   * Remove roles
   *
   * @param \Itk\ApiBundle\Entity\Role $roles
   */
  public function removeRole(\Itk\ApiBundle\Entity\Role $roles)
  {
    $this->roles->removeElement($roles);
  }

  /**
   * Get roles
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getRoles()
  {
    return $this->roles;
  }
}
