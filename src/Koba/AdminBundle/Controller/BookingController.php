<?php
/**
 * @file
 * Contains BookingController.
 */

namespace Koba\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/bookings")
 */
class BookingController extends Controller {
  /**
   * indexAction.
   *
   * @Route("")
   *
   * @return array
   */
  public function indexAction() {
    $arr = $this->get('itk.exchange_xml_service')->parseXMLFile();

    return $arr;
  }
}
