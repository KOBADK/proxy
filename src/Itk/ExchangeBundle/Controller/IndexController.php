<?php
/**
 * @file
 * Test controller to debug the services during development.
 *
 * THIS FILE HAVE TO BE REMOVED BEFORE PRODUCTION.
 */

namespace Itk\ExchangeBundle\Controller;

use Itk\ExchangeBundle\Entity\Booking;
use Itk\ExchangeBundle\Entity\Resource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("")
 */
class IndexController extends Controller {
  /**
   * indexAction.
   *
   * @Route("/book/{offset}")
   */
  public function indexAction($offset = 0) {
        // Build resource for our test resource.
    $resource = $this->get('itk.exchange_resource_repository')->findOneByMail('DOKK1-lokale-test1@aarhus.dk');

    $userName = $this->container->getParameter('itk_exchange_user_name');
    $mail = $this->container->getParameter('itk_exchange_user_mail');

    // Create a test booking.
    $booking = new Booking();
    $booking->setSubject('Møde om nogle vigtige ting.');
    $booking->setDescription('Her beskriver vi hvad det er vi skal mødes om.');
    $booking->setName($userName);
    $booking->setMail($mail);
    $booking->setStartTime(time() + ($offset * 1800));
    $booking->setEndTime(time() + 1800 + ($offset  * 1800));
    $booking->setResource($resource);

    $provider = $this->get('itk.exchange_mail_service');
    $provider->createBooking($booking);

    return new JsonResponse(array('msg' => 'booking mail sent'));
  }

  /**
   * @Route("/rooms")
   */
  public function listResources() {

    $ad = $this->get('itk.exchange_ad');

    print_r($ad->getResources());

    return new JsonResponse(array('stest' => 'rewt'));
  }

  /**
   * @Route("/list")
   */
  public function getResources() {
    $resource_id = 'DOKK1-lokale-test1@aarhus.dk';
    $ws = $this->get('itk.exchange_web_service');

    $ws->getRessourceBookings($resource_id, mktime(0, 0, 0), mktime(23, 59, 59));

    return new JsonResponse(array('stest' => 'rewt'));
  }

  /**
   * @Route("/get")
   */
  public function getResource(Request $request) {
    $ws = $this->get('itk.exchange_web_service');

    $id = $request->query->get('id');
    $key = $request->query->get('key');

    $ws->getBooking($id, $key);


    return new JsonResponse(array('stest' => 'rewt'));
  }
}
