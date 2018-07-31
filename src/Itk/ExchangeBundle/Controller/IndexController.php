<?php

namespace Itk\ExchangeBundle\Controller;

use Doctrine\ORM\NonUniqueResultException;
use Itk\ExchangeBundle\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("")
 */
class IndexController extends Controller
{
    /**
     * indexAction.
     *
     * @Route("/book/{offset}")
     */
    public function indexAction($offset = 0)
    {
        try {
            // Build resource for our test resource.
            $resource = $this->get('itk.exchange_resource_repository')
                ->findOneByMail('DOKK1-lokale-test1@aarhus.dk');

            $userName = $this->container->getParameter(
                'itk_exchange_user_name'
            );
            $mail = $this->container->getParameter('itk_exchange_user_mail');

            // Create a test booking.
            $booking = new Booking();
            $booking->setSubject('Møde om nogle vigtige ting.');
            $booking->setDescription(
                'Her beskriver vi hvad det er vi skal mødes om.'
            );
            $booking->setName($userName);
            $booking->setMail($mail);
            $booking->setStartTime(time() + ($offset * 1800));
            $booking->setEndTime(time() + 1800 + ($offset * 1800));
            $booking->setResource($resource);
            $booking->setStatusPending();

            $provider = $this->get('itk.exchange_mail_service');
            $provider->createBooking($booking);

            return new JsonResponse(array('msg' => 'booking mail sent'));
        } catch (NonUniqueResultException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @Route("/cancel/{uid}")
     */
    public function cancelBooking(Request $request, $uid)
    {
        try {
            $resource = $this->get('itk.exchange_resource_repository')
                ->findOneByMail('DOKK1-lokale-test1@aarhus.dk');

            // Create a test booking.
            $booking = new Booking();
            $booking->setIcalUid($uid);
            $booking->setResource($resource);

            $provider = $this->get('itk.exchange_mail_service');
            $provider->cancelBooking($booking);

            return new JsonResponse(array('msg' => 'cancel booking mail sent'));
        } catch (NonUniqueResultException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @Route("/ad_resources")
     */
    public function listResources()
    {
        $ad = $this->get('itk.exchange_ad');

        return new JsonResponse(array('ad_resources' => $ad->getResources()));
    }

    /**
     * @Route("/bookings")
     */
    public function getResources()
    {
        try {
            $resource = $this->get('itk.exchange_resource_repository')
                ->findOneByMail('DOKK1-lokale-test1@aarhus.dk');
            $exchange = $this->get('itk.exchange_service');
            $calendar = $exchange->getResourceBookings(
                $resource,
                mktime(0, 0, 0),
                mktime(23, 59, 29),
                true
            );

            return new JsonResponse(
                array('bookings' => $calendar->getBookings())
            );
        } catch (NonUniqueResultException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @Route("/get_booking")
     */
    public function getResource(Request $request)
    {
        try {
            $resource = $this->get('itk.exchange_resource_repository')
                ->findOneByMail('DOKK1-lokale-test1@aarhus.dk');
            $exchange = $this->get('itk.exchange_service');

            $id = $request->query->get('id');
            $key = $request->query->get('key');

            $booking = $exchange->getBooking($resource, $id, $key);

            return new JsonResponse(array('booking' => $booking));
        } catch (NonUniqueResultException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }
    }
}
