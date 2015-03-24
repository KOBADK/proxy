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
}
