<?php
/**
 * @file
 * Library to handle communication with the Exchange server through ICal
 * formatted mails.
 *
 * This class is used to make changes at the Exchange server as policies don't
 * allow us to preform write operations via the Exchange Web Service (EWS).
 */

namespace Itk\ExchangeBundle\Services;

use Itk\ExchangeBundle\Entity\Booking;
use Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ExchangeBookingService
 *
 * @package Itk\ExchangeBundle
 */
class ExchangeMailService
{
    /**
     * The service account information to use.
     *
     * @var array
     */
    private $account;

    /**
     * Mail client and ICalender generator.
     */
    private $mailer;
    private $ics;

    /**
     * Constructor
     *
     * @param $username
     *   Service account username.
     * @param $password
     *   Service account password.
     * @param $mail
     *   Service account mail address.
     * @param $mailer
     *   The mailer used to send mail to Exchange.
     * @param $ics
     *   The ICal provider, which is used generate vevent's.
     */
    public function __construct($username, $password, $mail, $mailer, $ics)
    {
        $this->account = array(
            'username' => $username,
            'password' => $password,
            'mail' => $mail,
        );

        $this->mailer = $mailer;
        $this->ics = $ics;
    }

    /**
     * Create a booking at Exchange.
     *
     * @param \Itk\ExchangeBundle\Entity\Booking $booking
     *   The booking entity to use in the request.
     */
    public function createBooking(Booking $booking)
    {
        // Get a new ICal calender object.
        $calendar = $this->createCalendar('REQUEST');

        // Create new event in the calender.
        $event = $calendar->newEvent();

        // Set reference to the ics event to be able to send cancel events later.
        $booking->setIcalUid($event->getProperty('UID'));

        // Encode booking information in the vevent description.
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());
        $normalizers[0]->setIgnoredAttributes(
            array(
                'resource',
                'exchangeId',
                'apiKey',
                'status',
            )
        );
        $serializer = new Serializer($normalizers, $encoders);
        $description = '<!-- KOBA '.base64_encode(
                $serializer->serialize($booking, 'json')
            ).' KOBA -->';

        // Set start date with correct timezone.
        $startDate = \DateTime::createFromFormat('U', $booking->getStartTime());
        $startDate->setTimeZone(new \DateTimeZone('UTC'));

        // Set end date with correct timezone.
        $endDate = \DateTime::createFromFormat('U', $booking->getEndTime());
        $endDate->setTimeZone(new \DateTimeZone('UTC'));

        // Set event information.
        $event->setStartDate($startDate)
            ->setEndDate($endDate)
            ->setName($booking->getSubject())
            ->setDescription($description)
            ->setLocation($booking->getResource()->getName());

        // Get the raw iCalCreator event.
        $rawEvent = $event->getEvent();
        $rawEvent->setOrganizer(
            $this->account['mail'],
            array('CN' => $this->account['username'])
        );
        $rawEvent->setClass('PUBLIC');

        // Set event mode.
        $rawEvent->setProperty('transp', 'OPAQUE');

        // Set description that will make Exchange pick-up the other description.
        $rawEvent->setProperty('X-ALT-DESC;FMTTYPE=text/plain', $description);

        // Get the calendar as an formatted string and send mail.
        $this->sendMail(
            $booking->getResource()->getMail(),
            $booking->getSubject(),
            $calendar->returnCalendar(),
            'REQUEST'
        );
    }

    /**
     * Cancel an exiting booking.
     *
     * @param \Itk\ExchangeBundle\Entity\Booking $booking
     *   The booking entity to cancel.
     *
     * @throws \Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException
     */
    public function cancelBooking(Booking $booking)
    {
        // Get a new ICal calender object.
        $calendar = $this->createCalendar('CANCEL');

        // Set start date with correct timezone.
        $startDate = \DateTime::createFromFormat('U', $booking->getStartTime());
        $startDate->setTimeZone(new \DateTimeZone('UTC'));

        // Set end date with correct timezone.
        $endDate = \DateTime::createFromFormat('U', $booking->getEndTime());
        $endDate->setTimeZone(new \DateTimeZone('UTC'));

        // Create new event in the calender.
        $event = $calendar->newEvent();
        $event->setStartDate($startDate)
            ->setEndDate($endDate)
            ->setStatus('CANCELLED');

        // Set event information.
        $e = $event->getEvent();
        $e->setProperty('UID', $booking->getIcalUid());

        // Get the calendar as an formatted string and send mail.
        $this->sendMail(
            $booking->getResource()
                ->getMail(),
            $booking->getSubject(),
            $calendar->returnCalendar(),
            'CANCEL'
        );
    }

    /**
     * Update or edit and existing booking.
     *
     * @throws \Itk\ExchangeBundle\Exceptions\ExchangeNotSupportedException
     */
    public function editBooking()
    {
        throw new ExchangeNotSupportedException();
    }

    /**
     * Creates a new iCalcreator calendar object.
     *
     * @param string $method
     *   The method to set in the calender (REQUEST, CANCELLED).
     *
     * @return mixed
     *   The calender object.
     */
    private function createCalendar($method)
    {
        // Create timezone.
        $tz = $this->ics->createTimezone();
        $tz->setTzid('UTC')
            ->setProperty('X-LIC-LOCATION', $tz->getTzid());

        // Create calender.
        $calendar = $this->ics->createCalendar($tz);

        // Set request method.
        $calendar->setMethod(strtoupper($method));

        return $calendar;
    }

    /**
     * Send mail to Exchange.
     *
     * @param $to
     *   The resource that should receive the mail.
     * @param $subject
     *   The mails subject.
     * @param $body
     *   The message/body to send.
     * @param string $method
     *   The method to set in the header to Exchange.
     */
    private function sendMail($to, $subject, $body, $method = 'REQUEST')
    {
        // Create mail message.
        $message = $this->mailer->createMessage()
            ->setSubject($subject)
            ->setFrom($this->account['mail'])
            ->setSender($this->account['mail'])
            ->setReturnPath($this->account['mail'])
            ->setTo($to)
            ->setBody($body, 'text/calendar');

        // Set the required headers.
        $headers = $message->getHeaders();
        $type = $headers->get('Content-Type');
        $type->setValue('text/calendar');
        $type->setParameters(
            array(
                'charset' => 'utf-8',
                'method' => $method,
            )
        );

        // Send the mail.
        $this->mailer->send($message);
    }
}
