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
use Koba\MainBundle\Entity\ApiKey;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * @Route("/apikeys")
 */
class ApiKeyController extends Controller {
  /**
   * Get all ApiKeys.
   *
   * @FOSRest\Get("")
   *
   * @return array
   *   Array of ApiKeys.
   */
  public function getApiKeys() {
    return $this->get('koba.apikey_repository')->findAll();
  }

  /**
   * Get ApiKey by $key.
   *
   * @FOSRest\Get("/{key}")
   *
   * @param $key
   *   The apikey.
   *
   * @return ApiKey
   */
  public function getApiKey($key) {
    $apiKeyEntity = $this->get('koba.apikey_repository')->findOneByApiKey($key);

    if ($apiKeyEntity === null) {
      throw new NotFoundHttpException('apikey not found', null, 404);
    }

    return $apiKeyEntity;
  }

  /**
   * Save a new ApiKey.
   *
   * @FOSRest\Post("")
   *
   * @param Request $request
   *   The Http Request.
   */
  public function postApiKey(Request $request) {
    $content = json_decode($request->getContent());

    $manager = $this->getDoctrine()->getEntityManager();

    $apiKey = new ApiKey();
    $apiKey->setApiKey($content->apikey);
    $apiKey->setConfiguration($content->configuration);
    $manager->persist($apiKey);

    $manager->flush();
  }
}
