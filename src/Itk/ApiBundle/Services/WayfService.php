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
 * Class BookingsService
 *
 * @package Itk\ApiBundle\Services
 */
class WayfService {
  protected $config;
  protected $container;

  /**
   * Function __construct.
   *
   * @param array $config
   *   config : configuration
   */
  public function __construct(Container $container) {
    $this->container = $container;

    $configPath = $this->container->get('kernel')->getRootDir() . '/config/';
    $this->config['idp_certificate'] = file_get_contents($configPath . $this->container->getParameter('wayf_idp_certificate'));
    $this->config['sso'] = $this->container->getParameter('wayf_sso');
    $this->config['private_key'] = file_get_contents($configPath . $this->container->getParameter('wayf_private_key'));
    $this->config['asc'] = $this->container->getParameter('wayf_asc');
    $this->config['entityid'] = $this->container->getParameter('wayf_entityid');
  }

  /**
   * Function authenticate.
   *
   * @param array $providerids
   *   providerids: list of providers
   *
   * @return array
   *   Reponse or output.
   */
  public function authenticate($providerids = array()) {
    if (isset($_POST['SAMLResponse'])) {
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
    else {
      // Handle SAML request.
      $id = '_' . sha1(uniqid(mt_rand(), TRUE));
      $issue_instant = gmdate('Y-m-d\TH:i:s\Z', time());
      $sp = $this->config['entityid'];
      $asc = $this->config['asc'];
      $sso = $this->config['sso'];
      // Add scoping.
      $scoping = '';
      foreach ($providerids as $provider) {
        $scoping .= "<samlp:IDPEntry ProviderID=\"$provider\"/>";
      }
      if ($scoping) {
        $scoping = '<samlp:Scoping><samlp:IDPList>' . $scoping . '</samlp:IDPList></samlp:Scoping>';
      }
      // Construct request.
      $request = <<<eof
<?xml version="1.0"?>
<samlp:AuthnRequest
    ID="$id"
    Version="2.0"
    IssueInstant="$issue_instant"
    Destination="$sso"
    AssertionConsumerServiceURL="$asc"
    ProtocolBinding="urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"
    xmlns:samlp="urn:oasis:names:tc:SAML:2.0:protocol">
    <saml:Issuer xmlns:saml="urn:oasis:names:tc:SAML:2.0:assertion">$sp</saml:Issuer>
    $scoping
</samlp:AuthnRequest>
eof;

      // Construct request.
      $querystring = "SAMLRequest=" . urlencode(base64_encode(gzdeflate($request)));
      $querystring .= '&SigAlg=' . urlencode('http://www.w3.org/2000/09/xmldsig#rsa-sha1');

      // Get private key.
      $key = openssl_pkey_get_private($this->config['private_key']);
      if (!$key) {
        throw new SPortoException('Invalid private key used');
      }

      // Sign the request.
      $signature = "";
      openssl_sign($querystring, $signature, $key, OPENSSL_ALGO_SHA1);
      openssl_free_key($key);

      // Send request.
      header('Location: ' . $this->config['sso'] . "?" . $querystring . '&Signature=' . urlencode(base64_encode($signature)));
      exit;
    }
  }

  /**
   * Function extractAttributes.
   *
   * @param [object] $xp
   *   xp : samlresponse
   *
   * @return [array]
   *   array with attributes
   */
  protected function extractAttributes($xp) {
    $res = array();
    // Grab attributes from AttributeSattement.
    $attributes  = $xp->query("/samlp:Response/saml:Assertion/saml:AttributeStatement/saml:Attribute");
    foreach ($attributes as $attribute) {
      $valuearray = array();
      $values = $xp->query('./saml:AttributeValue', $attribute);
      foreach ($values as $value) {
        $valuearray[] = $value->textContent;
      }
      $res[$attribute->getAttribute('Name')] = $valuearray;
    }
    return $res;
  }
  /**
   * Function verifySignature.
   *
   * @param [object]  $xp
   *   xp: samlresponse
   * @param bool $assertion
   *   assertion : should assertsions be checked.
   *
   * @return [null]
   *   exceptions are thrown if something fails.
   */
  protected function verifySignature($xp, $assertion = TRUE) {
    $status = $xp->query('/samlp:Response/samlp:Status/samlp:StatusCode/@Value')->item(0)->value;
    if ($status != 'urn:oasis:names:tc:SAML:2.0:status:Success') {
      $statusmessage = $xp->query('/samlp:Response/samlp:Status/samlp:StatusMessage')->item(0);
      throw new SPortoException('Invalid samlp response<br/>' . $statusmessage->C14N(TRUE, FALSE));
    }
    if ($assertion) {
      $context = $xp->query('/samlp:Response/saml:Assertion')->item(0);
    }
    else {
      $context = $xp->query('/samlp:Response')->item(0);
    }
    // Get signature and digest value.
    $signaturevalue = base64_decode($xp->query('ds:Signature/ds:SignatureValue', $context)->item(0)->textContent);
    $digestvalue    = base64_decode($xp->query('ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestValue', $context)->item(0)->textContent);
    $signedelement  = $context;
    $signature      = $xp->query("ds:Signature", $signedelement)->item(0);
    $signedinfo     = $xp->query("ds:SignedInfo", $signature)->item(0)->C14N(TRUE, FALSE);
    $signature->parentNode->removeChild($signature);
    $canonicalxml = $signedelement->C14N(TRUE, FALSE);
    // Get IdP certificate.
    $publickey = openssl_get_publickey($this->config['idp_certificate']);
    if (!$publickey) {
      throw new SPortoException('Invalid public key used');
    }
    // Verify signature.
    if (!((sha1($canonicalxml, TRUE) == $digestvalue) && @openssl_verify($signedinfo, $signaturevalue, $publickey) == 1)) {
      throw new SPortoException('Error verifying incoming SAMLResponse');
    }
  }
  /**
   * Function validateResponse.
   *
   * @param [object] $xp
   *   xp : samlresponse
   *
   * @return [null]
   *   [exceptions are thrown if something fails]
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
    $ashortwhileago = gmdate('Y-m-d\TH:i:s\Z', time() - $skew);
    $inashortwhile = gmdate('Y-m-d\TH:i:s\Z', time() + $skew);
    $assertion = $xp->query('/samlp:Response/saml:Assertion')->item(0);
    $subjectconfirmationdata_notbefore = $xp->query('./saml:Subject/saml:SubjectConfirmation/saml:SubjectConfirmationData/@NotBefore', $assertion);
    if ($subjectconfirmationdata_notbefore->length  && $ashortwhileago < $subjectconfirmationdata_notbefore->item(0)->value) {
      $issues[] = 'SubjectConfirmation not valid yet';
    }
    $subjectconfirmationdata_notonorafter = $xp->query('./saml:Subject/saml:SubjectConfirmation/saml:SubjectConfirmationData/@NotOnOrAfter', $assertion);
    if ($subjectconfirmationdata_notonorafter->length && $inashortwhile >= $subjectconfirmationdata_notonorafter->item(0)->value) {
      $issues[] = 'SubjectConfirmation too old';
    }
    $conditions_notbefore = $xp->query('./saml:Conditions/@NotBefore', $assertion);

    if ($conditions_notbefore->length && $ashortwhileago > $conditions_notbefore->item(0)->value) {
      $issues[] = 'Assertion Conditions not yet valid';
    }
    $conditions_notonorafter = $xp->query('./saml:Conditions/@NotOnOrAfter', $assertion);
    if ($conditions_notonorafter->length && $ashortwhileago >= $conditions_notonorafter->item(0)->value) {
      $issues[] = 'Assertions Condition too old';
    }
    $authstatement_sessionnotonorafter = $xp->query('./saml:AuthStatement/@SessionNotOnOrAfter', $assertion);
    if ($authstatement_sessionnotonorafter->length && $ashortwhileago >= $authstatement_sessionnotonorafter->item(0)->value) {
      $issues[] = 'AuthnStatement Session too old';
    }
    if (!empty($issues)) {
      throw new SPortoException('Problems detected with response. ' . PHP_EOL . 'Issues: ' . PHP_EOL . implode(PHP_EOL, $issues));
    }
  }
}

class SPortoException extends \Exception {}
