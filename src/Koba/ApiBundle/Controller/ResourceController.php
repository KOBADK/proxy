<?php
/**
 * @file
 * Contains ResourceController.
 */

namespace Koba\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * @Route("/resources")
 */
class ResourceController extends FOSRestController {
  /**
   * Get all resources the apikey with group id has access to.
   *
   * @FOSRest\Get("/group/{groupID}", defaults={"groupID" = "default"})
   *
   * @param Request $request
   *   The request object.
   * @param string $groupID
   *   The id of the group to get resources for.
   *   Defaults to default
   *
   * @View(serializerGroups={"admin"})
   *
   * @return array
   */
  public function getResourcesForGroupAction(Request $request, $groupID) {
    // Confirm the apikey is accepted.
    $apiKey = $this->get('koba.apikey_service')->getApiKey($request->query->get('apikey'));

    $configuration = $apiKey->getConfiguration();

    $resources = array();

    foreach ($configuration['groups'] as $group) {
      if ($group['id'] === $groupID) {
        $resources = $group['resources'];
        break;
      }
    }

    return $resources;
  }

  /**
   * @FOSRest\Get("/{resourceMail}/group/{groupId}/bookings/from/{from}/to/{to}")
   *
   * @param Request $request
   *   The request.
   * @param $groupId
   *   The group id.
   * @param $resourceMail
   *   The mail of the resource.
   * @param $from
   *   The from time (unix timestamp).
   * @param $to
   *   The to time (unix timestamp).
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function getResourceBookings(Request $request, $groupId, $resourceMail, $from, $to) {
    $apiKeyService = $this->get('koba.apikey_service');

    // Confirm the apikey is accepted.
    $apiKey = $apiKeyService->getApiKey($request->query->get('apikey'));

    // Get resource configuration and check Access.
    $resourceConfiguration = $apiKeyService->getResourceConfiguration($apiKey, $groupId, $resourceMail);

    // Get the resource. We get it here to avoid more injections in the service.
    $resource = $this->get('doctrine')->getRepository('ItkExchangeBundle:Resource')->findOneByMail($resourceMail);

    $calendarService = $this->get('koba.calendar_service');

    // Get calendar content.
    $content = $calendarService->getCalendar($apiKey, $groupId, $resource, $resourceConfiguration, $from, $to);

    return new JsonResponse($content);
  }


  /**
   * @FOSRest\Get("/{resourceMail}/group/{groupId}/freebusy/from/{from}/to/{to}")
   *
   * @param Request $request
   *   The request.
   * @param $groupId
   *   The group id.
   * @param $resourceMail
   *   The mail of the resource.
   * @param $from
   *   The from time (unix timestamp).
   * @param $to
   *   The to time (unix timestamp).
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function getResourceFreeBusy(Request $request, $groupId, $resourceMail, $from, $to) {
    $apiKeyService = $this->get('koba.apikey_service');

    // Confirm the apikey is accepted.
    $apiKey = $apiKeyService->getApiKey($request->query->get('apikey'));

    // Check Access.
    $apiKeyService->getResourceConfiguration($apiKey, $groupId, $resourceMail);

    // Get the resource. We get it here to avoid more injections in the service.
    $resource = $this->get('itk.exchange_resource_repository')->findOneByMail($resourceMail);

    $exchangeService = $this->get('itk.exchange_service');

    $content = $exchangeService->getBookingsForResource($resource, $from, $to, FALSE);

    $bookings = array();

    foreach ($content->getBookings() as $b) {
      $bookings[] = (object) array('start' => $b->getStart(), 'end' => $b->getEnd());
    }

    return new JsonResponse($bookings);
  }
}
