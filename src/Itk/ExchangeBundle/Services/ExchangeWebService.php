<?php
/**
 * @file
 * Contains the Itk ExchangeService
 */

namespace Itk\ExchangeBundle\Services;

/**
 * Class ExchangeWS
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeWS {

  /**
   * Constructor
   */
  public function __construct() {

  }

  public function getRessources() {
    throw new ExchangeNotSupported();
  }

  public function getRessource() {
    throw new ExchangeNotSupported();
  }
}
