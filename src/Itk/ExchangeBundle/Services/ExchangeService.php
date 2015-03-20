<?php
/**
 * @file
 * Wrapper service for the more specialized exchanges services.
 *
 * This wrapper exists as the methods used to communication with Exchange is
 * split between sending ICal formatted mails and pull the Exchange server via
 * the EWS reset API.
 */

namespace Itk\ExchangeBundle\Services;

/**
 * Class ExchangeService
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeService {
  /**
   * Get all resources from Exchange.
   */
  public function getResources() {
    // @TODO: Call correct method.
    return array(
      array(
        "name" => "DOKK1-lokale-test1",
        "mail" => "DOKK1-lokale-test1@aarhus.dk"
      ),
      array(
        "name" => "DOKK1-test-udstyr",
        "mail" => "DOKK1-test-udstyr@aarhus.dk"
      ),
    );
  }
}
