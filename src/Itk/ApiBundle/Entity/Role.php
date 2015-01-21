<?php

namespace Itk\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\XmlRoot;

use Symfony\Component\Validator\Constraints AS Assert;

/**
 * A user role. Used to decide which resources a user has access to.
 *
 * @ORM\Entity
 * @ORM\Table(name="koba_role")
 * @XmlRoot("role")
 */
class Role {
  /**
   * Internal role ID
   *
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   *
   * @Groups({"role"})
   */
  protected $id;

  /**
   * The title of the role
   *
   * @ORM\Column(type="string", nullable=false)
   *
   * @Assert\NotBlank
   *
   * @Groups({"role"})
   */
  protected $title;

  /**
   * The description of the role
   *
   * @ORM\Column(type="text")
   *
   * @Assert\NotBlank

   * @Groups({"role"})
   */
  protected $description;

  /**
   * Resources the role has access to
   *
   * @ORM\ManyToMany(targetEntity="Resource", mappedBy="roles")
   *
   * @Assert\Collection
   **/
  protected $resources;

  /**
   * Users that have this role
   *
   * @ORM\ManyToMany(targetEntity="User", mappedBy="roles")
   *
   * @Assert\Collection
   **/
  protected $users;

  /**
   * Constructor
   */
  public function __construct() {
    $this->resources = new \Doctrine\Common\Collections\ArrayCollection();
    $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
   * Set title
   *
   * @param string $title
   * @return Role
   */
  public function setTitle($title) {
    $this->title = $title;

    return $this;
  }

  /**
   * Get title
   *
   * @return string
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Set description
   *
   * @param string $description
   * @return Role
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
   * Add resource
   *
   * @param \Itk\ApiBundle\Entity\Resource $resource
   * @return Role
   */
  public function addResource(\Itk\ApiBundle\Entity\Resource $resource) {
    $resource->addRole($this);
    $this->resources[] = $resource;

    return $this;
  }

  /**
   * Remove resource
   *
   * @param \Itk\ApiBundle\Entity\Resource $resource
   */
  public function removeResource(\Itk\ApiBundle\Entity\Resource $resource) {
    $resource->removeRole($this);
    $this->resources->removeElement($resource);
  }

  /**
   * Get resources
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getResources() {
    return $this->resources;
  }

  /**
   * Add user
   *
   * @param \Itk\ApiBundle\Entity\User $user
   * @return Role
   */
  public function addUser(\Itk\ApiBundle\Entity\User $user) {
    $user->addRole($this);
    $this->users[] = $user;

    return $this;
  }

  /**
   * Remove user
   *
   * @param \Itk\ApiBundle\Entity\User $user
   */
  public function removeUser(\Itk\ApiBundle\Entity\User $user) {
    $user->removeRole($this);
    $this->users->removeElement($user);
  }

  /**
   * Get users
   *
   * @return \Doctrine\Common\Collections\Collection
   */
  public function getUsers() {
    return $this->users;
  }
}
