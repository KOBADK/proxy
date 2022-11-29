<?php
/**
 * @file
 * Simple SOAP client build around CURL to communicate with Exchange EWS.
 */

namespace Itk\ExchangeBundle\Services;

use Exception;
use GuzzleHttp\Client;
use Itk\ExchangeBundle\Exceptions\ExchangeSoapException;
use Psr\SimpleCache\CacheInterface;

/**
 * Class ExchangeSoapClientService.
 *
 * @package Itk\ExchangeBundle\Services
 */
class ExchangeSoapClientService
{
    const USER_AGENT = 'ExchangeWebService KOBA';
    const CACHE_KEY_TOKEN = 'exchange-token';

    /**
     * Connection information about Exchange EWS.
     *
     * @var array
     */
    private $exchange;

    private $namespaces;
    private $curlOptions = array();

    private $clientId;
    private $clientSecret;
    private $tenantId;
    private $username;
    private $password;

    private $cache;

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
    public function __construct(
        CacheInterface $cache,
        $host,
        $username,
        $password,
        $clientId,
        $clientSecret,
        $tenantId,
        $version = 'Exchange2010'
    ) {
        // Set account information to the Exchange EWS.
        $this->exchange = array(
            'host' => $host,
            'version' => $version,
            'username' => $username,
            'password' => $password,
        );

        $this->username = $username;
        $this->password = $password;
        $this->tenantId = $tenantId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        $this->cache = $cache;

        // Set default options.
        $this->curlOptions = array(
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => 1,
        );

        // Set EWS namespaces.
        $this->namespaces = array(
            'xsd' => 'http://www.w3.org/2001/XMLSchema',
            'soap' => 'http://schemas.xmlsoap.org/soap/envelope/',
            't' => 'http://schemas.microsoft.com/exchange/services/2006/types',
            'm' => 'http://schemas.microsoft.com/exchange/services/2006/messages',
        );
    }

    public function getAuthenticationToken() {
        $token = $this->cache->get(self::CACHE_KEY_TOKEN);

        if ($token === null) {
            $client = new Client();

            $url = 'https://login.microsoftonline.com/'.$this->tenantId.'/oauth2/v2.0/token';

            $response = $client->post($url, [
                    'form_params' => [
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'scope' => 'https://outlook.office365.com/EWS.AccessAsUser.All',
                        'username' => $this->username,
                        'password' => $this->password,
                        'grant_type' => 'password',
                    ],
                ]
            );

            $contents = json_decode($response->getBody()->getContents(), true);

            $token = $contents['access_token'];

            $this->cache->set(self::CACHE_KEY_TOKEN, $token, $contents['expires_in']);
        }

        return $token;
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
    public function request(
        $action,
        $xmlBody,
        $impersonationId = null,
        $options = array()
    ) {
        // Merge options with defaults.
        $options = $options + $this->curlOptions;

        // Run the XML through DOMDocument to get an verify on the format.
        $doc = new \DOMDocument();
        $doc->loadXML($this->generateSoapMessage($xmlBody, $impersonationId));

        // Render and store the final request string.
        $requestBodyString = $doc->saveXML();

        $headers = [];
        if ($impersonationId !== null) {
            $headers = [
                'X-AnchorMailbox: '.$impersonationId,
            ];
        }

        // Send the SOAP request to the server via CURL.
        return $this->curlRequest($action, $requestBodyString, $options, $headers);
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
     * @param array $headers
     *   Headers to sent with the request.
     *
     * @return mixed
     *   The RAW XML response.
     */
    private function curlRequest($action, $requestBody, $options, $headers = [])
    {
        $token = $this->getAuthenticationToken();

        // Set headers.
        $headers = array_merge($headers, [
            'Method: POST',
            'Connection: Keep-Alive',
            'User-Agent: Symfony-Exchange-Soap-Client',
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: "'.$action.'"',
            'Authorization: Bearer '.$token,
        ]);
        $options[CURLOPT_HTTPHEADER] = $headers;

        // Set request content.
        $options[CURLOPT_POSTFIELDS] = $requestBody;

        // Initialise and configure cURL.
        $ch = curl_init($this->exchange['host'].'/EWS/Exchange.asmx');
        curl_setopt_array($ch, $options);

        // Send the request.
        $response = curl_exec($ch);

        // Check if request went well.
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code !== 200 || $response === false) {
            $erroNo = curl_errno($ch);
            if (!$erroNo) {
                throw new ExchangeSoapException('HTTP error - '.$code . ': ' . $response . ' ==> ' . $requestBody, $code);
            }
            throw new ExchangeSoapException(curl_error($ch), $erroNo);
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
    private function generateSoapMessage($xmlBody, $impersonationId = null)
    {
        // Build namespace string.
        $ns_string = '';
        foreach ($this->namespaces as $key => $url) {
            if ($key == '') {
                $ns_string .= ' xmlns="'.$url.'"';
            } else {
                $ns_string .= ' xmlns:'.$key.'="'.$url.'"';
            }
        }

        // Build impersonation if needed.
        $impersonation = '';
        if (is_string($impersonationId)) {
            $impersonation = '<t:ExchangeImpersonation><t:ConnectingSID><t:PrimarySmtpAddress>'.$impersonationId.'</t:PrimarySmtpAddress></t:ConnectingSID></t:ExchangeImpersonation>';
        }

        // Build the final message.
        $message = array(
            'header' => '<?xml version="1.0" encoding="UTF-8"?>',
            'env_start' => '<soap:Envelope'.$ns_string.'>',
            'soap_header_start' => '<soap:Header>',
            'version' => '<t:RequestServerVersion Version ="'.$this->exchange['version'].'"/>',
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
