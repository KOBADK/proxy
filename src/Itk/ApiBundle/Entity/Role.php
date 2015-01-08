<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="koba_role")
 */
class Role {
  /**
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\Column(type="string")
   */
  protected $title;

  /**
   * @ORM\Column(type="text")
   */
  protected $description;

  /**
   * @ORM\ManyToMany(targetEntity="Resource", mappedBy="roles")
   **/
  protected $resources;

  /**
   * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
   **/
  protected $users;

  /**
   * Constructor
   */
  public function __construct()
  {
    $this->resources = new \Doctrine\Common\Collections\ArrayCollection();
    $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set title
   *
   * @param string $title
   * @return Role
   */
  public function setTitle($title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Set description
   *
   * @param string $description
   * @return Role
   */
  public function setDescription($description)
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Get description
   *
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Add resources
   *
   * @param \Itk\ApiBundle\Entity\Resource $resources
   * @return Role
   */
  public function addResource(\Itk\ApiBundle\Entity\Resource $resources)
  {
    $this->resources[] = $resources;

    return $this;
  }

  /**
   * Remove resources
   *
   * @param \Itk\ApiBundle\Entity\Resource $resources
   */
  public function removeResource(\Itk\ApiBundle\Entity\Resource $resources)
  {
    $this->resources->removeElement($resources);
  }

  /**
   * Get resources
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getResources()
  {
    return $this->resources;
  }

  /**
   * Add users
   *
   * @param \Itk\ApiBundle\Entity\User $users
   * @return Role
   */
  public function addUser(\Itk\ApiBundle\Entity\User $users)
  {
    $this->users[] = $users;

    return $this;
  }

  /**
   * Remove users
   *
   * @param \Itk\ApiBundle\Entity\User $users
   */
  public function removeUser(\Itk\ApiBundle\Entity\User $users)
  {
    $this->users->removeElement($users);
  }

  /**
   * Get users
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getUsers()
  {
    return $this->users;
  }
}
