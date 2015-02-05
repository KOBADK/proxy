<?php
/**
 * @file
 * Logged out event.
 */

namespace Itk\WayfBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class WayfLoggedOutEvent.
 *
 * @package Itk\WayfBundle\Event
 */
class WayfLoggedOutEvent extends Event {
  protected $response;
  protected $status;

  /**
   * Default constructor.
   *
   * @param string $response
   *   The RAW XML response from WAYF.
   * @param bool $status
   *   The logged out status.
   */
  public function __construct($response, $status) {
    $this->message = $response;
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
   * Status on the success of the logout request.
   *
   * @return bool
   *   If TRUE the user was logged out else FALSE.
   */
  public function getStatus() {
    return $this->status;
  }
}
