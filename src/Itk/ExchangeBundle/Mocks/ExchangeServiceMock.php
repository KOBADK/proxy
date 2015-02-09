<?php
/**
 * @file
 * Contains the exchange mock.
 */
namespace Itk\ExchangeBundle\Services;

use Symfony\Component\DependencyInjection\Container;
use Koba\MainBundle\Entity\Booking;
use Koba\MainBundle\Entity\Resource;
use Koba\MainBundle\Entity\User;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class ExchangeServiceMock
 *
 * @package Itk\ApiBundle\Services
 */
class ExchangeServiceMock extends ExchangeService {
  protected $container;
  protected $doctrine;
  protected $em;
  protected $helperService;
  protected $ewsHeaders;

  /**
   * Constructor
   *
   * @param Container $container
   *   @TODO Missing description?
   * @param HelperService $helperService
   *   @TODO Missing description?
   */
  function __construct(Container $container, HelperService $helperService) {
    $this->helperService = $helperService;

    // @todo: The service is only dependent on the container to get the entity
    // manager?
    $this->container = $container;

    // @TODO: Inject "EntityManager $em" -> "@doctrine.orm.entity_manager" so
    // it's not dependent on doctrine inside the service.
    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();

    $this->ewsHeaders = "Content-Type:text/calendar; charset=utf-8; method=REQUEST\r\n";
    $this->ewsHeaders .= "Content-Type: text/plain; charset=\"utf-8\" \r\n";
  }

  /**
   * Get a resource from exchange
   *
   * @param string $mail the mail that identifies the resource in Exchange
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
   */
  public function getResource($mail) {
    if ($mail === 'resource1@test.test') {

    }
  }

  /**
   * Mock of sendBookingRequest
   *
   * @todo: What is this "subject" about?
   * subject = 'success'
   * subject = 'error_mail_not_received'
   *
   * @param Booking $booking The booking to attempt to make
   *   @TODO Missing description?
   * @return array
   *   @TODO Missing description?
   */
  public function sendBookingRequest(Booking $booking) {
    if ($booking->getSubject() === 'success') {
      $booking->setStatusMessage('Mail sent');
      $booking->setCompleted(true);
      $booking->setEid("123123");
      $this->em->flush();
      return $this->helperService->generateResponse(201, $booking);
    }
    else if ($booking->getSubject() === 'error_mail_not_received') {
      $booking->setStatusMessage('Mail not received by resource');
      $this->em->flush();
      return $this->helperService->generateResponse(503, null, array('message' => 'Booking request was not delivered to resource, try again'));
    }
  }
}
