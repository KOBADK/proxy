<?php
/**
 * @file
 * Custom event used to indicate events during wayf actions.
 */

namespace Itk\WayfBundle\Event;

/**
 * Class WayfEvents.
 *
 * Defines the events that the WayfBundle can dispatch.
 *
 * @package Itk\WayfBundle
 */
final class WayfEvents {
  /**
   * User logged in event.
   */
  const WAYF_LOGGED_IN = 'itk_wayf.loggedin';

  /**
   * User logged out event.
   */
  const WAYF_LOGGED_OUT = 'itk_wayf.loggedout';
}
