<?php
/**
 * @file
 * Search the LDAP/AD server for information about resources available.
 */

namespace Itk\ExchangeBundle\Services;

/**
 * Class ExchangeADService
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeADService {

  private $ldap;
  private $binding;
  private $host;
  private $username;
  private $password;
  private $connected;

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
    $this->host = $host;
    $this->username = $username;
    $this->password = $password;
    $this->connected = false;
  }

  /**
   * Close the connection to LDAP.
   */
  public function __destruct() {
    $this->disconnect();
  }

  /**
   *
   */
  private function disconnect() {
    if ($this->connected) {
      // Close connection to the ldap server.
      ldap_close($this->ldap);
    }
  }

  /**
   * Connect to LDAP if not already connected.
   */
  private function connect() {
    if (!$this->connected) {
      // Connect to the ldap server.
      $this->ldap = ldap_connect($this->host) or die("Could not connect to LDAP server.\n");
      $this->binding = @ldap_bind($this->ldap, 'ADM' . "\\" . $this->username, $this->password);

      if ($this->binding) {
        $this->connected = true;
      }
    }
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
    // Connect to LDAP.
    $this->connect();

    $baseDn = 'OU=ResursePostkasser,OU=Brugere,OU=Postkalender,OU=Operatoerer,DC=adm,DC=aarhuskommune,DC=dk';
    $filter = '(!(accountname=*))';

    $result = ldap_search($this->ldap, $baseDn, $filter);

    // Get the entries.
    $info = ldap_get_entries($this->ldap, $result);

    // Loop over the results getting the information needed.
    $resources = array();
    for ($i=0; $i < $info['count']; $i++) {
      if (isset($info[$i]['mail'][0]) &&  isset($info[$i]['cn'][0])) {
        // Set the resources mail address as key and the friendly name as value.
        $resources[$info[$i]['mail'][0]] = $info[$i]['cn'][0];
      }
    }

    return $resources;
  }
}
