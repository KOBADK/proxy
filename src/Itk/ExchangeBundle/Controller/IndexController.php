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
   * @Route("/cancel")
   */
  public function cancelBooking(Request $request) {

    $uid = $request->query->get('uid');

    $resource = $this->get('itk.exchange_resource_repository')->findOneByMail('DOKK1-lokale-test1@aarhus.dk');

    // Create a test booking.
    $booking = new Booking();
    $booking->setIcalUid($uid);
    $booking->setResource($resource);

    $provider = $this->get('itk.exchange_mail_service');
    $provider->cancelBooking($booking);

    return new JsonResponse(array('stest' => 'rewt'));
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
    $resource = $this->get('itk.exchange_resource_repository')->findOneByMail('DOKK1-lokale-test1@aarhus.dk');
    $exchange = $this->get('itk.exchange_service');
    $calendar = $exchange->getBookingsForResource($resource, mktime(0, 0, 0), mktime(23, 59, 29), TRUE);

    print_r($calendar);

    return new JsonResponse(array('stest' => 'rewt'));
  }

  /**
   * @Route("/get")
   */
  public function getResource(Request $request) {
    $exchange = $this->get('itk.exchange_service');

    $id = $request->query->get('id');
    $key = $request->query->get('key');

    $booking = $exchange->getBooking($id, $key);
    print_r($booking);

    return new JsonResponse(array('stest' => 'rewt'));
  }

  /**
   * @Route("/test")
   */
  public function testBooking(Request $request) {
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
    $booking->setStartTime('1429173225');
    $booking->setEndTime('1429175025');
    $booking->setResource($resource);

    $exchange = $this->get('itk.exchange_service');
    $exchange->isBookingAccepted($booking);

    return new JsonResponse(array('stest' => 'rewt'));
  }
}
