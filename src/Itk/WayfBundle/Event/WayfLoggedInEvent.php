<?php
/**
 * @file
 * Logged out event.
 */

namespace Itk\WayfBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class WayfLoggedInEvent.
 *
 * @package Itk\WayfBundle\Event
 */
class WayfLoggedInEvent extends Event {
  protected $response;
  protected $attributes;

  /**
   * Default constructor.
   *
   * @param string $response
   *   The RAW XML response from WAYF.
   * @param array $attributes
   *   The parsed attributes.
   */
  public function __construct($response, array $attributes) {
    $this->message = $response;
    $this->attributes = $attributes;
  }

  /**
   * Get the RAW XML response.
   *
   * @return string
   *   The XML response.
   */
  public function getResponse() {
    return $this->response;
  }

  /**
   * Get all attributes.
   *
   * @return array
   *   The attributes.
   */
  public function getAttributes() {
    return $this->attributes;
  }

  /**
   * Get at single attribute.
   *
   * @param string $attribute
   *   The name of the attribute to get.
   *
   * @return bool|mixed
   *   If the attribute exists it is returned else FALSE.
   */
  public function getAttribute($attribute) {
    if (isset($this->attributes[$attribute])) {
      return $this->attributes[$attribute];
    }
    return FALSE;
  }
}
