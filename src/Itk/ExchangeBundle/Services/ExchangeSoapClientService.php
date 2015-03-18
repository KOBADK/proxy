<?php
/**
 * @file
 * Simple SOAP client build around CURL to communicate with Exchange EWS.
 */

namespace Itk\ExchangeBundle\Services;

use Itk\ExchangeBundle\Exceptions\ExchangeSoapException;

/**
 * Class ExchangeSoapClientService.
 *
 * @package Itk\ExchangeBundle\Services
 */
class ExchangeSoapClientService {
  const XML_HEADER = '<?xml version="1.0" encoding="UTF-8"?>';
  const USER_AGENT = 'ExchangeWebService KOBA';

  private $host;
  private $username;
  private $password;
  private $version;

  private $namespaces;
  private $curlOptions = array();

  /**
   * Construct the SOAP client.
   *
   * @param string $host
   *   The host to connect to.
   * @param string $username
   *   The username to use.
   * @param string $password
   *   The password to match the username.
   * @param string $version
   *   The Exchange version.
   */
  public function __construct($host, $username, $password, $version = 'Exchange2010') {
    $this->host = $host;
    $this->version = $version;

    $this->username = $username;
    $this->password = $password;

    // Set default options.
    $this->curlOptions = array(
      CURLOPT_SSL_VERIFYPEER => FALSE,
      CURLOPT_SSL_VERIFYHOST => FALSE,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_POST => TRUE,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_HTTPAUTH => CURLAUTH_BASIC | CURLAUTH_NTLM,
      CURLOPT_USERPWD => $this->username . ':' . $this->password,
      CURLOPT_CONNECTTIMEOUT => 10,
    );

    // Set namespaces.
    $this->namespaces = array(
      'xsd' => 'http://www.w3.org/2001/XMLSchema',
      'soap' => 'http://schemas.xmlsoap.org/soap/envelope/',
      't' => 'http://schemas.microsoft.com/exchange/services/2006/types',
      'm' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
    );
  }

  /**
   * Preform request against the Exchange EWS service.
   *
   * @param string $action
   *   SOAP action header.
   * @param string $xmlBody
   *   The XML body request
   * @param string|null $impersonationId
   *   The impersonation id (normally the room id as a mail address).
   * @param array $options
   *   Extra options for the transport client.
   *
   * @return mixed
   *   The RAW XML response.
   */
  public function request($action, $xmlBody, $impersonationId = NULL, $options = array()) {
    // Merge options with defaults.
    $options = $options + $this->curlOptions;

    // Run the XML through DOMDocument to get an verify on the format.
    $doc = new \DOMDocument();
    $doc->loadXML($this->generateSoapMessage($xmlBody, $impersonationId));

    // Render and store the final request string.
    $requestBodyString = $doc->saveXML();

    // Send the SOAP request to the server via CURL.
    return $this->curlRequest($action, $requestBodyString, $options);
  }

  /**
   * Send request via CURL.
   *
   * @param string $action
   *   SOAP action header.
   * @param string $requestBody
   *   The request XML message.
   * @param array $options
   *   Extra options for the transport client.
   *
   * @return mixed
   *   The RAW XML response.
   */
  private function curlRequest($action, $requestBody, $options) {
    // Set headers.
    $headers = array(
      'Method: POST',
      'Connection: Keep-Alive',
      'User-Agent: Symfony-Exchange-Soap-Client',
      'Content-Type: text/xml; charset=utf-8',
      'SOAPAction: "' . $action .'"',
    );
    $options[CURLOPT_HTTPHEADER] = $headers;

    // Set request content.
    $options[CURLOPT_POSTFIELDS] = $requestBody;

    // Initialise and configure cURL.
    $ch = curl_init($this->host . '/EWS/Exchange.asmx');
    curl_setopt_array($ch, $options);

    // Send the request.
    $response = curl_exec($ch);

    // Check if request went well.
    if ($response === FALSE) {
      throw new ExchangeSoapException(curl_error($ch), curl_errno($ch));
    }

    // Close the cURL instance before we return.
    curl_close($ch);

    return $response;
  }

  /**
   * Generate SOAP message.
   *
   * @param string $xmlBody
   *   The XML message inside the body tag.
   * @param string|null $impersonationId
   *   The mail address of the user to impersonate.
   *
   * @return string
   *   The final XML message.
   */
  private function generateSoapMessage($xmlBody, $impersonationId = NULL) {
    // Build namespace string.
    $ns_string = '';
    foreach ($this->namespaces as $key => $url) {
      if ($key == '') {
        $ns_string .= ' xmlns="' . $url . '"';
      }
      else {
        $ns_string .= ' xmlns:' . $key . '="' . $url . '"';
      }
    }

    // Build impersonation if needed.
    $impersonation = '';
    if (is_string($impersonationId)) {
      $impersonation = '<t:ExchangeImpersonation><t:ConnectingSID><t:PrimarySmtpAddress>' . $impersonationId . '</t:PrimarySmtpAddress></t:ConnectingSID></t:ExchangeImpersonation>';
    }

    // Build the final message.
    $message = array(
      'header' => self::XML_HEADER,
      'env_start' => '<soap:Envelope' . $ns_string . '>',
      'soap_header_start' => '<soap:Header>',
      'version' => '<t:RequestServerVersion Version ="'. $this->version .'"/>',
      'tz' => '<t:TimeZoneContext><t:TimeZoneDefinition Id="Central Europe Standard Time"/></t:TimeZoneContext>',
      'im' => $impersonation,
      'soap_header_end' => '</soap:Header>',
      'body_start' => '<soap:Body>',
      'body' => $xmlBody,
      'body_end' => '</soap:Body>',
      'env_end' => '</soap:Envelope>',
    );

    return implode("\n", $message);
  }
}
