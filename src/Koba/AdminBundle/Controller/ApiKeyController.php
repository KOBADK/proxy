<?php
/**
 * @file
 * Contains apiKeys controller for AdminBundle.
 */

namespace Koba\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @Route("/apikeys")
 */
class ApiKeyController extends Controller {
  /**
   * @Route("/{key}")
   *
   * @param $key
   *   The
   */
  public function getApiKey($key) {
    $apiKeyEntity = $this->get('koba.apikey_repository')->findOneByApiKey($key);

    if ($apiKeyEntity === null) {
      throw new NotFoundHttpException('apikey not found', null, 404);
    }

    return $apiKeyEntity;
  }
}
