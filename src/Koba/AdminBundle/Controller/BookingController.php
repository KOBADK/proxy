<?php
/**
 * @file
 * Contains BookingController.
 */

namespace Koba\AdminBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;

/**
 * @Route("/bookings")
 */
class BookingController extends FOSRestController
{
    /**
     * Get xml bookings.
     *
     * Test function.
     *
     * @FOSRest\Get("/xml")
     *
     * @return array
     */
    public function getXmlBookings()
    {
        $arr = $this->get('itk.exchange_xml_service')->importXmlFile(
            'test.xml'
        );

        return $arr;
    }
}
