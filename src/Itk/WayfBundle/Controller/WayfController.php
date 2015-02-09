<?php
/**
 * @file
 * Defines the callbacks need to when log in/out of the wayf.dk services which
 * communicates via SAML messages and redirects.
 */

namespace Itk\WayfBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\View\View;
use Itk\WayfBundle\Event\WayfEvents;
use Itk\WayfBundle\Event\WayfLoggedInEvent;
use Itk\WayfBundle\Event\WayfLoggedOutEvent;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Itk\ApiBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @Route("/wayf")
 */
class WayfController extends Controller {
  /**
   * Tries to login the use by redirect the use to the WAYF SingleSignOn service.
   *
   * @Get("/login")
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirect to WAYF login with status 303 (see other).
   */
  public function getLogin() {
    $wayfService = $this->get('itk.wayf_service');

    // Get the base64 encode message as an location URL.
    $location = $wayfService->login();

    // Redirect the user to the WAYF login location. We send an 303 (See other)
    // status code, which is not allowed to be cached by browser.
    return $this->redirect($location, 303);
  }

  /**
   * Assertion Consumer Service end-point that WAYF post back to.
   *
   * @Post("/login")
   *
   * @param Request $request
   *   Symfony request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   JSON response with the
   */
  public function assertionConsumerService(Request $request) {
    // Parse and verify post data from WAYF.
    $wayfService = $this->get('itk.wayf_service');
    $result = $wayfService->consume();

    // Send WAYF logged in event.
    $event = new WayfLoggedInEvent($result['response'], $result['attributes']);
    $this->get('event_dispatcher')->dispatch(WayfEvents::WAYF_LOGGED_IN, $event);

    // Return a reply to the end user.
    return new JsonResponse(array('message' => 'success'), 200);
  }

  /**
   * Send logout request to WAYF.
   *
   * @Get("/logout")

   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Symfony request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirect to WAYF login with status 303 (see other). If the request
   *   contains the SAML response it will return a JSON response with the
   *   status.
   */
  function logout(Request $request) {
    $wayfService = $this->get('itk.wayf_service');

    // Check if the is a logout callback from WAYF. This is a little hack as
    // wayf.dk don't supports POST callback on logout.
    $result = $wayfService->loggedOut();
    if ($result) {
      // Send WAYF logged out event.
      $event = new WayfLoggedOutEvent($result['response'], $result['status']);
      $this->get('event_dispatcher')->dispatch(WayfEvents::WAYF_LOGGED_OUT, $event);

      return new JsonResponse(array('message' => 'logged out success'), $result['status']);
    }
    else {
      // Get the base64 encode message as an location URL.
      $location = $wayfService->logout();

      // Redirect the user to the WAYF logout location. We send an 303 (See other)
      // status code, which is not allowed to be cached by browser.
      return $this->redirect($location, 303);
    }
  }

  /**
   * Generate XML metadata for the service provider.
   *
   * @Get("/metadata")
   */
  function metadata() {
    $wayfService = $this->get('itk.wayf_service');

    $response = new Response($wayfService->getMetadata());
    $response->headers->set('Content-Type', 'text/xml');
    return $response;
  }
}
