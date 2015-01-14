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
use Itk\ApiBundle\Entity\User;
use Itk\ApiBundle\Entity\Role;
use Itk\ApiBundle\Entity\Resource;
use Itk\ApiBundle\Entity\Booking;

/**
 * Class HelperService
 *
 * @package Itk\ApiBundle\Services
 */
class HelperService {
  protected $container;

  /**
   * Constructor.
   *
   * @param Container $container
   */
  function __construct(Container $container) {
    $this->container = $container;
  }

  /**
   * Generate service response
   *
   * @param $status
   * @param null $data
   * @param null $errors
   * @return array
   */
  public function generateResponse($status, $data = null, $errors = null) {
    if ($errors) {
      $data = array('errors' => array($errors));
    }

    return array(
      'status' => $status,
      'data' => $data
    );
  }

  /**
   * Validate user
   *
   * @param \Itk\ApiBundle\Entity\User $user
   * @return array
   */
  public function validateUser(User $user) {
    $validator = $this->container->get('validator');
    $errors = $validator->validate($user);

    $status = count($errors) > 0 ? 400 : 200;

    return array(
      'errors' => $errors,
      'status' => $status
    );
  }

  /**
   * Validate role
   *
   * @param \Itk\ApiBundle\Entity\Role $role
   * @return array
   */
  public function validateRole(Role $role) {
    $validator = $this->container->get('validator');
    $errors = $validator->validate($role);

    $status = count($errors) > 0 ? 400 : 200;

    return array(
      'errors' => $errors,
      'status' => $status
    );
  }

  /**
   * Validate resource
   *
   * @param \Itk\ApiBundle\Entity\Resource $resource
   * @return array
   */
  public function validateResource(Resource $resource) {
    $validator = $this->container->get('validator');
    $errors = $validator->validate($resource);

    $status = count($errors) > 0 ? 400 : 200;

    return array(
      'errors' => $errors,
      'status' => $status
    );
  }

  /**
   * Validate booking
   *
   * @param \Itk\ApiBundle\Entity\Booking $booking
   * @return array
   */
  public function validateBooking(Booking $booking) {
    $validator = $this->container->get('validator');
    $errors = $validator->validate($booking);

    $status = count($errors) > 0 ? 400 : 200;

    return array(
      'errors' => $errors,
      'status' => $status
    );
  }

}