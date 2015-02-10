<?php

namespace Koba\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\MaxDepth;
use JMS\Serializer\Annotation\XmlRoot;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * A user group. Used to decide which resources a user has access to.
 *
 * @ORM\Entity(repositoryClass="Koba\MainBundle\Entity\GroupRepository")
 * @ORM\Table(name="koba_group")
 * @XmlRoot("group")
 */
class Group {
  /**
   * Internal id
   *
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   *
   * @Groups({"group"})
   */
  protected $id;

  /**
   * The title of the group
   *
   * @ORM\Column(type="string", nullable=false)
   *
   * @Assert\NotBlank
   *
   * @Groups({"group"})
   */
  protected $title;

  /**
   * The description of the group
   *
   * @ORM\Column(type="text")
   *
   * @Assert\NotBlank
   * @Groups({"group"})
   */
  protected $description;

  /**
   * Resources the group has access to
   *
   * @ORM\ManyToMany(targetEntity="Resource", mappedBy="groups")
   *
   * @Assert\Collection
   **/
  protected $resources;

  /**
   * Users that have this group
   *
   * @ORM\ManyToMany(targetEntity="User", mappedBy="groups")
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
   *
   * @return group
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
   *
   * @return group
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
   * @param \Koba\MainBundle\Entity\Resource $resource
   *
   * @return group
   */
  public function addResource(\Koba\MainBundle\Entity\Resource $resource) {
    $resource->addGroup($this);
    $this->resources[] = $resource;

    return $this;
  }

  /**
   * Remove resource
   *
   * @param \Koba\MainBundle\Entity\Resource $resource
   */
  public function removeResource(\Koba\MainBundle\Entity\Resource $resource) {
    $resource->removeGroup($this);
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
   * @param \Koba\MainBundle\Entity\User $user
   *
   * @return group
   */
  public function addUser(\Koba\MainBundle\Entity\User $user) {
    $user->addGroup($this);
    $this->users[] = $user;

    return $this;
  }

  /**
   * Remove user
   *
   * @param \Koba\MainBundle\Entity\User $user
   */
  public function removeUser(\Koba\MainBundle\Entity\User $user) {
    $user->removeGroup($this);
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
