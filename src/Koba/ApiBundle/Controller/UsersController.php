<?php
/**
 * @file
 * Contains the users controller for /api.
 */

namespace Koba\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use Koba\MainBundle\Entity\Booking;
use Doctrine\DBAL\Types\BooleanType;

/**
 * @Route("/user")
 */
class UsersController extends FOSRestController {
  /**
   * Get current user.
   *
   * @Get("")
   *
   * @ApiDoc(
   *  description="Get current user",
   *  statusCodes={
   *    200="Returned when successful",
   *    404="Returned when no users are found"
   *  }
   * )
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   Response object.
   *
   * // TODO: implement this!
   */
  public function getCurrentUser() {
    $usersService = $this->get('koba.users_service');

    $view = $this->view(array(), 200);
    return $this->handleView($view);
  }
}
