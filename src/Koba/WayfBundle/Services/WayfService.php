<?php
/**
 * @file
 * Handles communication with WAYF.dk identity provider to allow single sign on
 * and single sign of.
 *
 * @TODO: Where is the redirects handle in routes? Should this be a bundle in
 * it self?
 */

namespace Koba\WayfBundle\Services;

use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class WayfService.
 *
 * @package Itk\ApiBundle\Services
 */
class WayfService {

  protected $config;
  protected $container;
  protected $templating;


  /**
   * Construct.
   *
   * @param \Symfony\Component\Templating\EngineInterface $templating
   * @param \Symfony\Component\DependencyInjection\Container $container
   */
  public function __construct(EngineInterface $templating, Container $container) {
    $this->templating = $templating;

    // @todo: Change to use setters.
    $this->container = $container;

    $configPath = $this->container->get('kernel')->getRootDir() . '/config/';
    $this->config['idp_certificate'] = file_get_contents($configPath . $this->container->getParameter('wayf_idp_certificate'));
    $this->config['sso'] = $this->container->getParameter('wayf_sso');
    $this->config['privateKey'] = file_get_contents($configPath . $this->container->getParameter('wayf_private_key'));
    $this->config['asc'] = $this->container->getParameter('wayf_asc');
    $this->config['entityId'] = $this->container->getParameter('wayf_entityid');
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

  /**
   * Generate SAML request for WAYF.
   *
   * @throws WayfException
   */
  public function request() {
    // Handle SAML request.
    $id = '_' . sha1(uniqid(mt_rand(), TRUE));
    $issueInstant = gmdate('Y-m-d\TH:i:s\Z', time());
    $sp = $this->config['entityId'];
    $asc = $this->config['asc'];
    $sso = $this->config['sso'];

    // Construct request.
    $request = $this->render('WayfBundle:Default:login_request.xml.twig', $this->config);

    // Construct request.
    $queryString = "SAMLRequest=" . urlencode(base64_encode(gzdeflate($request)));
    $queryString .= '&SigAlg=' . urlencode('http://www.w3.org/2000/09/xmldsig#rsa-sha1');

    // Get private key.
    $key = openssl_pkey_get_private($this->config['privateKey']);
    if (!$key) {
      throw new WayfException('Invalid private key used');
    }

    // Sign the request.
    $signature = "";
    openssl_sign($queryString, $signature, $key, OPENSSL_ALGO_SHA1);
    openssl_free_key($key);

    // Send request.
    header('Location: ' . $this->config['sso'] . "?" . $queryString . '&Signature=' . urlencode(base64_encode($signature)));
    exit;
  }

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

    // Send logout request.
    header('Location: ' . $slo . "?" . $query . '&Signature=' . urlencode(base64_encode($signature)));
    exit;
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
    if ($destination != NULL && $destination != $this->config['asc']) {
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
   * Generate sp metadata based on configuration.
   *
   * @return string
   */
  function getMetadata() {
    return $this->render('WayfBundle:Default:metadata.xml.twig', $this->config);;
  }
}

/**
 * Class WayfException
 *
 * @package Itk\ApiBundle\Services
 */
class WayfException extends \Exception {
}
