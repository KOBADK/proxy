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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/resources")
 */
class ResourceController extends FOSRestController {
  /**
   * Get resources.
   *
   * @FOSRest\Get("/{groupID}", defaults={"groupID" = "default"})
   *
   * @param Request $request
   *   The request object.
   * @param string $groupID
   *   The id of the group to get resources for.
   *   Defaults to DEFAULT
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function getResources(Request $request, $groupID) {
    // Confirm the apikey is accepted.
    $apiKey = $this->get('koba.apikey_service')->getApiKey($request);

    $configuration = $apiKey->getConfiguration();

    $resources = array();

    foreach ($configuration['groups'] as $group) {
      if ($group['id'] === $groupID) {
        $resources = $group['resources'];
        break;
      }
    }

    $view = $this->view($resources, 200);
    return $this->handleView($view);
  }

  /**
   * @FOSRest\Get("/{resourceMail}/group/{groupId}/bookings")
   *
   * @param Request $request
   * @param $groupId
   * @param $resourceMail
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function getBookingsForResource(Request $request, $groupId, $resourceMail) {
    // Confirm the apikey is accepted.
    $apiKey = $this->get('koba.apikey_service')->getApiKey($request);

    $allowAccess = false;
    foreach ($apiKey->getConfiguration()['groups'] as $group) {
      if ($group['id'] === $groupId) {
        foreach ($group['resources'] as $resource) {
          if ($resource['mail'] === $resourceMail) {
            $allowAccess = true;
            break;
          }
        }
      }
    }
    if (!$allowAccess) {
      throw new AccessDeniedException();
    }

    $resource = $this->get('doctrine')->getRepository('ItkExchangeBundle:Resource')->findOneByMail($resourceMail);

    $calendarService = $this->get('koba.calendar_service');

    $content = $calendarService->getCalendar($resource->getMail(), $resource->getName());

    return new JsonResponse($content);
  }
}
