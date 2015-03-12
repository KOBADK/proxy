<?php
/**
 * @file
 * Contains apiKeys controller for AdminBundle.
 */

namespace Koba\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Koba\MainBundle\Entity\ApiKey;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    $postApiKey = $content->api_key;
    $postConfiguration = $content->configuration;
    $postName = $content->name;

    $apiKeyEntity = $this->get('koba.apikey_repository')->findOneByApiKey($postApiKey);

    if ($apiKeyEntity) {
      throw new ConflictHttpException('api key already exists', null, 409);
    }

    $manager = $this->getDoctrine()->getEntityManager();

    $apiKey = new ApiKey();
    $apiKey->setApiKey($postApiKey);
    $apiKey->setName($postName);
    $apiKey->setConfiguration($postConfiguration);
    $manager->persist($apiKey);

    $manager->flush();
  }

  /**
   * Update an ApiKey.
   *
   * @FOSRest\Put("/{key}")
   *
   * @param Request $request
   *   The Http Request.
   * @param $key
   *   Key of the ApiKey to update.
   *
   * @return Response
   *   The Http Response.
   */
  public function putApiKey(Request $request, $key) {
    $apiKeyEntity = $this->get('koba.apikey_repository')->findOneByApiKey($key);

    if (!$apiKeyEntity) {
      throw new NotFoundHttpException("api key not found", null, 404);
    }

    $content = json_decode($request->getContent());

    $postConfiguration = $content->configuration;
    $postName = $content->name;

    $manager = $this->getDoctrine()->getEntityManager();

    $apiKeyEntity->setConfiguration($postConfiguration);
    $apiKeyEntity->setName($postName);

    $manager->flush();

    $resp = new Response();
    $resp->setStatusCode(201);
    return $resp;
  }

  /**
   * Delete an ApiKey.
   *
   * @FOSRest\Delete("/{key}")
   *
   * @param $key
   *   The id of the ApiKey to delete
   */
  public function deleteApiKey($key) {
    $apiKeyEntity = $this->get('koba.apikey_repository')->findOneByApiKey($key);

    if (!$apiKeyEntity) {
      throw new NotFoundHttpException("api key not found", null, 404);
    }

    $manager = $this->getDoctrine()->getEntityManager();
    $manager->remove($apiKeyEntity);
    $manager->flush();
  }
}
