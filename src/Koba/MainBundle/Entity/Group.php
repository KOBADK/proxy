<?php
/**
 * @file
 * Contains Group entity.
 */

namespace Koba\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * A Group
 *
 * @ORM\Entity(repositoryClass="Koba\MainBundle\Entity\GroupRepository")
 * @ORM\Table(name="koba_group")
 */
class Group {
  /**
   * Internal id
   *
   * @ORM\Column(type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * The title of the group
   *
   * @ORM\Column(type="string", nullable=false)
   */
  protected $title;

  /**
   * The description of the group
   *
   * @ORM\Column(type="text")
   */
  protected $description;

  /**
   * Constructor
   */
  public function __construct() {
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
}
