<?php
/**
 * @file
 * This file is a part of the Itk ApiBundle.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Itk\ApiBundle\Services;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class BookingsService
 *
 * @package Itk\ApiBundle\Services
 */
class BookingsService {
  protected $container;
  protected $doctrine;
  protected $em;
  protected $bookingRepository;
  protected $helperService;

  /**
   * Constructor
   *
   * @param Container $container
   * @param HelperService $helperService
   */
  function __construct(Container $container, HelperService $helperService) {
    $this->container = $container;
    $this->helperService = $helperService;
    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();
    $this->bookingRepository = $this->doctrine->getRepository('Itk\ApiBundle\Entity\Booking');
  }

  /**
   * Get all bookings
   *
   * @return array
   */
  public function getAllBookings() {
    $bookings = $this->bookingRepository->findAll();

    return $this->helperService->generateResponse(200, $bookings);
  }
}