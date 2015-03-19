<?php
/**
 * @file
 * Search the LDAP/AD server for information about resources available.
 */

namespace Itk\ExchangeBundle\Services;

use Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException;

/**
 * Class ExchangeADService
 *
 * @TODO: Build cache into the class to prevent asking the LDAP all the time...
 *        the information will not change often.
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeADService {

  private $ldap;
  private $binding;

  /**
   * Connect to the LDAP server.
   *
   * @param string $host
   *   The hostname or IP address for the LDAP server.
   * @param string $username
   *   The username to log into LDAP.
   * @param string $password
   *   The password matching the username.
   */
  public function __construct($host, $username, $password) {
    // Connect to the ldap server.
    $this->ldap = ldap_connect($host);
    $this->binding = @ldap_bind($this->ldap, 'ADM' . "\\" . $username, $password);
  }

  /**
   * Close the connection to LDAP.
   */
  public function __destruct() {
    // Close connection to the ldap server.
    ldap_close($this->ldap);
  }

  /**
   * Get all resources from LDAP classified as "room".
   *
   * @TODO: Look into the information from LDAP, some of it may be useful.
   *
   * @return array
   *   The resources indexed by mail and friendly name as value.
   */
  public function getResources() {
    // Search the LDAP for resource with the type room.
    $result = ldap_search($this->ldap, 'OU=ResursePostkasser,OU=Brugere,OU=Postkalender,OU=Operatoerer,DC=adm,DC=aarhuskommune,DC=dk', 'msExchResourceMetaData=ResourceType:room');

    // Get the information from the search.
    $info = ldap_get_entries($this->ldap, $result);

    // Loop over the results getting the information needed.
    $resources = array();
    for ($i=0; $i < $info["count"]; $i++) {
      // Set the resources mail address as key and the friendly name as value.
      $resources[$info[$i]["mail"][0]] = $info[$i]["cn"][0];
    }

    return $resources;
  }
}
