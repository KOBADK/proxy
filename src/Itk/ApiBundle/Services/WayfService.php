<?php
/**
 * @file
 * This file is a part of the Itk ApiBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Itk\ApiBundle\Services;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class WayfService
 *
 * @package Itk\ApiBundle\Services
 */
class WayfService {
  protected $config;
  protected $container;

  /**
   * Function __construct.
   *
   * @param Container $container
   *   Container.
   */
  public function __construct(Container $container) {
    $this->container = $container;

    $configPath = $this->container->get('kernel')->getRootDir() . '/config/';
    $this->config['idp_certificate'] = file_get_contents($configPath . $this->container->getParameter('wayf_idp_certificate'));
    $this->config['sso'] = $this->container->getParameter('wayf_sso');
    $this->config['privateKey'] = file_get_contents($configPath . $this->container->getParameter('wayf_private_key'));
    $this->config['asc'] = $this->container->getParameter('wayf_asc');
    $this->config['entityId'] = $this->container->getParameter('wayf_entityid');
  }

  /**
   * Log out from wayf
   *
   * @TODO: Make this!
   */
  public function logout() {
    return;
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
    $request = <<<eof
<?xml version="1.0"?>
<samlp:AuthnRequest
    ID="$id"
    Version="2.0"
    IssueInstant="$issueInstant"
    Destination="$sso"
    AssertionConsumerServiceURL="$asc"
    ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol">
    <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">$sp</saml:Issuer>
</samlp:AuthnRequest>
eof;

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
    $message = base64_decode($_POST['SAMLResponse']);
    $document = new \DOMDocument();
    $document->loadXML($message);
    $xp = new \DomXPath($document);
    $xp->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
    $xp->registerNamespace('samlp', 'urn:oasis:names:tc:SAML:2.0:protocol');
    $xp->registerNamespace('saml', 'urn:oasis:names:tc:SAML:2.0:assertion');
    $this->verifySignature($xp, TRUE);
    $this->validateResponse($xp);
    return array(
      'attributes' => $this->extractAttributes($xp),
      'response' => $message,
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
}

/**
 * Class WayfException
 *
 * @package Itk\ApiBundle\Services
 */
class WayfException extends \Exception {
}
