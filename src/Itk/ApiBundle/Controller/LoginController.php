<?php

namespace Itk\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\View\View;
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
 * @Route("/login")
 */
class LoginController extends FOSRestController {
  /**
   * @Get("")
   *
   * @ApiDoc(
   *  description="Send a user to WAYF",
   *  statusCodes={
   *    200="Returned when successful",
   *  }
   * )
   *
   */
  public function getLoginAction() {
    // TODO: check if user is already logged in

    // Send the user to WAYF.
    $wayfService = $this->get('koba.wayf_service');
    $wayfService->request();
  }

  /**
   * @Post("")
   *
   * @ApiDoc(
   *  description="Gets user back from WAYF",
   *  statusCodes={
   *    200="Returned when successful",
   *  }
   * )
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function PostLoginAction(Request $request) {
    // Parse and verify post data from WAYF.
    $wayfService = $this->get('koba.wayf_service');
    $result = $wayfService->response();

    // Set needed attributes.
    $mail = $result['attributes']['mail'][0];
    $firstName = $result['attributes']['gn'][0];
    $lastName = $result['attributes']['sn'][0];

    preg_match('/\d{10}$/', $result['attributes']['schacPersonalUniqueID'][0], $uniqueId);
    $uniqueId = reset($uniqueId);

    // TODO: HASH unique id

    // TODO: If first user, give ROLE_ADMIN

    // Save data to user entity.
    $userService = $this->container->get('koba.users_service');
    $user = $userService->getUserByUniqueId($uniqueId);

    if (!$user) {
      $user = new User();
      $user->setUniqueId($uniqueId);
    }

    $user->setName($firstName . " " . $lastName);
    $user->setMail($mail);
    $user->setStatus(true);

    // Persist user to database.
    $this->getDoctrine()->getManager()->persist($user);
    $this->getDoctrine()->getManager()->flush();

    // Set session token
    $token = new UsernamePasswordToken($user, null, 'main', array('ROLE_ADMIN'));
    $this->get('security.token_storage')->setToken($token);

    // Dispatch the login event
    $event = new InteractiveLoginEvent($request, $token);
    $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

    // Return a reply to the end user.
    return new JsonResponse(array('message' => 'success'), 200);
  }
}
