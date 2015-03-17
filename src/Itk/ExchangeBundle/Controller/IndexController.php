<?php
/**
 * @file
 * Contains index controller for MainBundle.
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
   * @Route("")
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
   * @Route("/list")
   */
  public function listResources() {

    $ws = $this->get('itk.exchange_web_service');

    $ws->getRessources();

    return new JsonResponse(array('stest' => 'rewt'));
  }

  /**
   * @Route("/get")
   */
  public function getResources() {
    $resource = 'DOKK1-lokale-test1@aarhus.dk';

    $ws = $this->get('itk.exchange_web_service');

    $ws->getRessources();

    return new JsonResponse(array('stest' => 'rewt'));
  }
}
