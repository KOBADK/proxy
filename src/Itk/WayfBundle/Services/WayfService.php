<?php
/**
 * @file
 * Handles communication with WAYF.dk identity provider to allow single sign on
 * and single sign of.
 */

namespace Itk\WayfBundle\Services;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\PhpFileCache;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class WayfService.
 *
 * @package Itk\WayfBundle\Services
 */
class WayfService {

  protected $cache;
  protected $templating;

  protected $certificate;
  protected $assertionConsumerService;
  protected $serviceProvider;
  protected $serviceProviderMetadata;

  protected $idpMode;
  protected $endpoints = array(
    'test' => 'https://testbridge.wayf.dk/saml2/idp/metadata.php',
    'qa' => 'https://betawayf.wayf.dk/saml2/idp/metadata.php',
    'production' => 'https://wayf.wayf.dk/saml2/idp/metadata.php',
  );

  /**
   * Construct.
   *
   * @param \Symfony\Component\Templating\EngineInterface $templating
   * @param \Doctrine\Common\Cache\Cache $cache
   */
  public function __construct(EngineInterface $templating, Cache $cache) {
    $this->templating = $templating;
    $this->cache = $cache;
  }

  /**
   * Set certificate information.
   *
   * @param string $cert
   *   The file path to the RSA certificate.
   * @param string $key
   *   The key to un-lock the certificate.
   */
  public function setCertificateInformation($cert, $key) {
    $this->certificate = array(
      'cert' => file_get_contents($cert),
      'key' => file_get_contents($key),
    );
  }

  /**
   * Set identity provider mode.
   *
   * @param $mode
   *   The mode to use can be 'test', 'qa' and 'production'.
   */
  public function setIdpMode($mode) {
    $this->idpMode = $mode;
  }

  /**
   * Set the assertion consumer service URL.
   *
   * This is the callback tha wayf post's information about the logged in user.
   *
   * @param $acs
   *   The URL to post back to.
   */
  public function setAssertionConsumerService($acs) {
    $this->assertionConsumerService = $acs;
  }

  /**
   * Set the service provider.
   *
   * @param $sp
   *   This would normally be the domain name for the current domain.
   */
  public function setServiceProvicer($sp) {
    $this->serviceProvider = $sp;
  }

  /**
   * Set the metadata required to generate this sites metadata.
   *
   * @param array $metadata
   *
   */
  public function setServiceProviderMetadata(array $metadata) {
    $this->serviceProviderMetadata = $metadata;
  }

  /**
   * Generate redirect URL with query string for login.
   *
   * The query string is the SAML request and signature for login at wayf.dk.
   *
   * @throws WayfException
   */
  public function login() {
    // Get identity provider metadata.
    $idpMetadata = $this->getIpdMetadata();

    // @TODO: Add support for scoping?
//    $scoping = '';
//    foreach ($providerids as $provider) {
//      $scoping .= "<samlp:IDPEntry ProviderID=\"$provider\"/>";
//    }
//    if ($scoping) {
//      $scoping = '<samlp:Scoping><samlp:IDPList>' . $scoping . '</samlp:IDPList></samlp:Scoping>';
//    }

    // Construct request.
    $request = $this->render('ItkWayfBundle::login_request.xml.twig', array(
      'id' => '_' . sha1(uniqid(mt_rand(), TRUE)),
      'issueInstant' => gmdate('Y-m-d\TH:i:s\Z', time()),
      'sso' => $idpMetadata['sso'],
      'acs' => $this->assertionConsumerService,
      'sp' => $this->serviceProvider,
    ));

    // Construct request.
    $queryString = "SAMLRequest=" . urlencode(base64_encode(gzdeflate($request)));
    $queryString .= '&SigAlg=' . urlencode('http://www.w3.org/2000/09/xmldsig#rsa-sha1');

    // Get private key.
    $key = openssl_pkey_get_private($this->certificate['key']);
    if (!$key) {
      throw new WayfException('Invalid private key used');
    }

    // Sign the request.
    $signature = "";
    openssl_sign($queryString, $signature, $key, OPENSSL_ALGO_SHA1);
    openssl_free_key($key);

    // Return the URL that the user should be redirected to.
    return $idpMetadata['sso'] . "?" . $queryString . '&Signature=' . urlencode(base64_encode($signature));  }

  /**
   * Parse SAML response.
   *
   * @return array
   *   Response.
   */
  public function response() {
    // Handle SAML response.
    $response = base64_decode($_POST['SAMLResponse']);

    $document = new \DOMDocument();
    $document->loadXML($response);

    $xp = new \DomXPath($document);
    $xp->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
    $xp->registerNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
    $xp->registerNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');

    $this->verifySignature($xp, TRUE);
    $this->validateResponse($xp);

    return array(
      'attributes' => $this->extractAttributes($xp),
      'response' => $response,
    );
  }

  /**
   * Logout using the current session information.
   *
   * @TODO: Can we use sessions?
   *
   * @throws \Itk\ApiBundle\Services\SPortoException
   */
  public function logout() {
    $id = '_' . sha1(uniqid(mt_rand(), TRUE));
    $issue_instant = gmdate('Y-m-d\TH:i:s\Z', time());
    $sp = $this->config['entityid'];
    $slo = $this->config['slo'];

    $ids = $_SESSION['wayf_dk_login'];

    // Construct request.
    $request = $this->render('WayfBundle:Default:logout_request.xml.twig', $this->config);

    // Construct request.
    $query = "SAMLRequest=" . urlencode(base64_encode(gzdeflate($request)));;
    $query .= '&SigAlg=' . urlencode('http://www.w3.org/2000/09/xmldsig#rsa-sha1');

    // Get private key.
    $key = openssl_pkey_get_private($this->config['private_key']);
    if (!$key) {
      throw new WayfException('Invalid private key used');
    }

    // Sign the request.
    $signature = "";
    openssl_sign($query, $signature, $key, OPENSSL_ALGO_SHA1);
    openssl_free_key($key);

    // Remove session information to end redirect loop in logout endpoint. This
    // assumes that we get logged out at WAYF. This is not optimal, but the best
    // we have.
    unset($_SESSION['wayf_dk_login']);

    // Return the URL that the user should be redirected to.
    return $slo . "?" . $query . '&Signature=' . urlencode(base64_encode($signature));
  }

  /**
   * Check if the user is logged in.
   *
   * @TODO: can we use session this way?
   *
   * As we don't know if the user is logged in we simply check if session WAYF
   * variable exists for the user. This don't grantee that the user is logged
   * into WAYF, but it's the best we have.
   */
  public function isLoggedIn() {
    $ids = $_SESSION['wayf_dk_login'];
    return (isset($ids['sessionIndex']) && isset($ids['nameID']));
  }

  /**
   * Generate service provider metadata based on configuration.
   *
   * @return string
   *   The metadata XML.
   */
  public function getMetadata() {
    return $this->render('ItkWayfBundle::metadata.xml.twig', array(
      'sp' => $this->serviceProvider,
      'cert' => preg_replace('/-{5}\w+\s\w+-{5}/', '', $this->certificate['cert']),
      'acs' => $this->assertionConsumerService,
      'logoutUrl' => $this->serviceProviderMetadata['logoutUrl'],
      'organizationName' => $this->serviceProviderMetadata['organizationName'],
      'organizationDisplayName' => $this->serviceProviderMetadata['organizationDisplayName'],
      'organizationUrl' => $this->serviceProviderMetadata['organizationUrl'],
      'organizationLanguage' => $this->serviceProviderMetadata['organizationLanguage'],
      'contactName' => $this->serviceProviderMetadata['contactName'],
      'contactMail' => $this->serviceProviderMetadata['contactMail'],
    ));
  }

  /**
   * Stores nameID and sessionID in drupal session.
   *
   * This information is needed to enabled logout from WAYF.dk.
   *
   * @TODO: can we use session this way?
   *
   * @param $xp
   *   xp : samlresponse
   */
  protected function storeSessionInformation($xp) {
    $assertion = $xp->query('/samlp:Response/saml:Assertion')->item(0);
    $_SESSION['wayf_dk_login'] = array(
      'nameID' => $xp->query('./saml:Subject/saml:NameID', $assertion)->item(0)->nodeValue,
      'sessionIndex' => $xp->query('./saml:AuthnStatement/@SessionIndex', $assertion)->item(0)->value,
    );
  }

  /**
   * Extract SAML attributes.
   *
   * @param \DomXPath $xp
   *   Response.
   *
   * @return array
   *   Array with attributes.
   */
  protected function extractAttributes($xp) {
    $res = array();
    // Grab attributes from AttributeSattement.
    $attributes = $xp->query("/samlp:Response/saml:Assertion/saml:AttributeStatement/saml:Attribute");
    foreach ($attributes as $attribute) {
      $values = array();
      $attributeValues = $xp->query('./saml:AttributeValue', $attribute);
      foreach ($attributeValues as $attributeValue) {
        $values[] = $attributeValue->textContent;
      }
      $res[$attribute->getAttribute('Name')] = $values;
    }
    return $res;
  }

  /**
   * Verify signature.
   *
   * @param \DomXPath $xp
   *   Response.
   *
   * @param bool $assertion
   *   Should assertsions be checked.
   *
   * @throws WayfException
   */
  protected function verifySignature($xp, $assertion = TRUE) {
    $status = $xp->query('/samlp:Response/samlp:Status/samlp:StatusCode/@Value')
      ->item(0)->value;
    if ($status != 'urn:oasis:names:tc:SAML:2.0:status:Success') {
      $statusMessage = $xp->query('/samlp:Response/samlp:Status/samlp:StatusMessage')
        ->item(0);
      throw new WayfException('Invalid samlp response<br/>' . $statusMessage->C14N(TRUE, FALSE));
    }

    if ($assertion) {
      $context = $xp->query('/samlp:Response/saml:Assertion')->item(0);
    }
    else {
      $context = $xp->query('/samlp:Response')->item(0);
    }
    // Get signature and digest value.
    $signatureValue = base64_decode($xp->query('ds:Signature/ds:SignatureValue', $context)
        ->item(0)->textContent);
    $digestValue = base64_decode($xp->query('ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestValue', $context)
        ->item(0)->textContent);
    $signedElement = $context;
    $signature = $xp->query("ds:Signature", $signedElement)->item(0);
    $signedInfo = $xp->query("ds:SignedInfo", $signature)
      ->item(0)
      ->C14N(TRUE, FALSE);
    $signature->parentNode->removeChild($signature);
    $canonicalXml = $signedElement->C14N(TRUE, FALSE);
    // Get IdP certificate.
    $publicKey = openssl_get_publickey($this->config['idp_certificate']);
    if (!$publicKey) {
      throw new WayfException('Invalid public key used');
    }
    // Verify signature.
    if (!((sha1($canonicalXml, TRUE) == $digestValue) && @openssl_verify($signedInfo, $signatureValue, $publicKey) == 1)) {
      throw new WayfException('Error verifying incoming SAMLResponse');
    }
  }

  /**
   * Function validateResponse.
   *
   * @param \DomXPath $xp
   *   Response.
   * @throws WayfException
   */
  protected function validateResponse($xp) {
    $issues = array();
    // Verify destination.
    $destination = $xp->query('/samlp:Response/@Destination')->item(0)->value;
    if ($destination != NULL && $destination != $this->config['acs']) {
      // Destination is optional.
      $issues[] = "Destination: $destination is not here; message not destined for us";
    }
    // Verify timestamps.
    $skew = 120;
    $aShortWhileAgo = gmdate('Y-m-d\TH:i:s\Z', time() - $skew);
    $inAShortWhile = gmdate('Y-m-d\TH:i:s\Z', time() + $skew);
    $assertion = $xp->query('/samlp:Response/saml:Assertion')->item(0);
    $subjectConfirmationDataNotBefore = $xp->query('./saml:Subject/saml:SubjectConfirmation/saml:SubjectConfirmationData/@NotBefore', $assertion);
    if ($subjectConfirmationDataNotBefore->length && $aShortWhileAgo < $subjectConfirmationDataNotBefore->item(0)->value) {
      $issues[] = 'SubjectConfirmation not valid yet';
    }
    $subjectConfirmationDataNotOnOrAfter = $xp->query('./saml:Subject/saml:SubjectConfirmation/saml:SubjectConfirmationData/@NotOnOrAfter', $assertion);
    if ($subjectConfirmationDataNotOnOrAfter->length && $inAShortWhile >= $subjectConfirmationDataNotOnOrAfter->item(0)->value) {
      $issues[] = 'SubjectConfirmation too old';
    }
    $conditionsNotBefore = $xp->query('./saml:Conditions/@NotBefore', $assertion);

    if ($conditionsNotBefore->length && $aShortWhileAgo > $conditionsNotBefore->item(0)->value) {
      $issues[] = 'Assertion Conditions not yet valid';
    }
    $conditionsNotOnOrAfter = $xp->query('./saml:Conditions/@NotOnOrAfter', $assertion);
    if ($conditionsNotOnOrAfter->length && $aShortWhileAgo >= $conditionsNotOnOrAfter->item(0)->value) {
      $issues[] = 'Assertions Condition too old';
    }
    $authStatementSessionNotOnOrAfter = $xp->query('./saml:AuthStatement/@SessionNotOnOrAfter', $assertion);
    if ($authStatementSessionNotOnOrAfter->length && $aShortWhileAgo >= $authStatementSessionNotOnOrAfter->item(0)->value) {
      $issues[] = 'AuthnStatement Session too old';
    }
    if (!empty($issues)) {
      throw new WayfException('Problems detected with response. ' . PHP_EOL . 'Issues: ' . PHP_EOL . implode(PHP_EOL, $issues));
    }
  }

  /**
   * Get identity provider metadata information.
   *
   * @return array|mixed
   *    The Single Sign On and Single Logout urls and the IDP public certificate.
   *
   * @throws \Itk\WayfBundle\Services\WayfException
   *   If the IDP do not return any data.
   */
  protected function getIpdMetadata() {
    $cache_key = 'ipd_' . $this->idpMode;
    $this->cache->setNamespace('itk_wayf.cache');

    // Try to get cached information.
    if (($info = $this->cache->fetch($cache_key)) === FALSE) {
      // Data not found in cache, so try to download it.
      @$metadata = file_get_contents($this->endpoints[$this->idpMode]);
      if ($metadata === FALSE) {
        throw new WayfException('An error occurred, WAYF metadata service not available.');
      }
      else {
        // Parse the XML metadata document.
        $xml = simplexml_load_string($metadata);
        $xml->registerXPathNamespace('md', 'urn:oasis:names:tc:SAML:2.0:metadata');
        $xml->registerXPathNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');

        $SsoDescriptor = '/md:EntityDescriptor/md:IDPSSODescriptor';
        $binding = 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect';

        // Get Single Sign On and logout urls.
        $sso = $xml->xpath("$SsoDescriptor/md:SingleSignOnService[@Binding='$binding']/@Location");
        $slo = $xml->xpath("$SsoDescriptor/md:SingleLogoutService[@Binding='$binding']/@Location");

        // Get certificate data.
        $cert = $xml->xpath("$SsoDescriptor/md:KeyDescriptor[@use='signing']/ds:KeyInfo/ds:X509Data/ds:X509Certificate");

        // Set information form the meta-date.
        $info = array(
          'cert' => (string) $cert[0],
          'sso' => (string) $sso[0]['Location'],
          'slo' => (string) $slo[0]['Location'],
        );

        // Save the information in the cache.
        $this->cache->save($cache_key, $info);
      }
    }

    return $info;
  }


  /**
   * Import organizations from the WAYF service.
   *
   * @TODO: Make this not Drupal.... use cache
   */
  function wayf_dk_login_organizations_list() {
    $feed_url = variable_get('wayf_dk_login_organizations_list_url', WAYF_DK_LOGIN_ORGANIZATIONS_LIST_URL);
    $content = file_get_contents($feed_url);
    $data = json_decode($content, TRUE);

    $data['https://testidp.wayf.dk/module.php/core/loginuserpass.php'] = array(
      'da' => 'WAYF test-institution (IDP)',
      'en' => 'WAYF test-institution (IDP)',
      'schacHomeOrganization' => 'testidp.wayf.dk',
    );

//    variable_set('wayf_dk_login_organizations_list', $data);
  }

  /**
   * Render XML message.
   *
   * @param string $view
   *   The view to use render the template.
   * @param array $parameters
   *   The parameters need be the template.
   *
   * @return string
   */
  protected function render($view, array $parameters = array()) {
    return $this->templating->render($view, $parameters);
  }
}

/**
 * Class WayfException
 *
 * @package Itk\ApiBundle\Services
 */
class WayfException extends \Exception {
}
