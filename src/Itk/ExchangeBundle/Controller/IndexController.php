<?php
/**
 * @file
 * Test controller to debug the services during development.
 *
 * THIS FILE HAVE TO BE REMOVED BEFORE PRODUCTION.
 */

namespace Itk\ExchangeBundle\Controller;

use Itk\ExchangeBundle\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("")
 */
class IndexController extends Controller {
  /**
   * indexAction.
   *
   * @Route("/book")
   */
  public function indexAction() {

    // Create some test data.
    $b = new Booking();
    $b->setSubject('New test event');
    $b->setDescription('Test event');
    $b->setName('Jesper Kristensen');
    $b->setMail('jeskr@aarhus.dk');
    $b->setStartTime(time());
    $b->setEndTime(time() + 3600);

    $provider = $this->get('itk.exchange_mail_service');
    $provider->createBooking($b);

    return new JsonResponse(array('stest' => 'rewt'));
  }

  /**
   * @Route("/rooms")
   */
  public function listResources() {

    $ws = $this->get('itk.exchange_web_service');

    $ws->getRessources();

    return new JsonResponse(array('stest' => 'rewt'));
  }

  /**
   * @Route("/list")
   */
  public function getResources() {
    $id = 'DOKK1-lokale-test1@aarhus.dk';
    $ws = $this->get('itk.exchange_web_service');

    $ws->getRessourceBookings($id, mktime(0, 0, 0), mktime(23, 59, 59));

    return new JsonResponse(array('stest' => 'rewt'));
  }

  /**
   * @Route("/get")
   */
  public function getResource() {
    $ws = $this->get('itk.exchange_web_service');

    $ws->getBooking("AAMkAGI0OWM5ZmE3LTBiOWMtNDg1Yi1iNmFlLTY5OGZhOGY0ZDI5NwBGAAAAAABLCXZAC9/fR7JGHNWMb+0pBwDpHfiAZp9LRYnG8zs4k/DGAAAAAAENAADpHfiAZp9LRYnG8zs4k/DGAAAoAqjXAAA=", "DwAAABYAAADpHfiAZp9LRYnG8zs4k/DGAAAoAq5Q");
  }
}
