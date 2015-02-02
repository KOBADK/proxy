<?php
/**
 * @file
 * @todo Missing file description?
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
   *   @TODO Missing description?
   */
  function __construct(Container $container) {
    // @TODO: container only used to get validator... consider to inject the
    // validator?
    $this->container = $container;
  }

  /**
   * Generate service response.
   *
   * @param $status
   *   @TODO Missing description?
   * @param null $data
   *   @TODO Missing description?
   * @param null $errors
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
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
   * Validate user.
   *
   * @param \Itk\ApiBundle\Entity\User $user
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
   */
  public function validateUser(User $user) {
    $validator = $this->container->get('validator');
    $errors = $validator->validate($user);

    // @todo: What do this magic calculation do?
    $status = count($errors) > 0 ? 400 : 200;

    return array(
      'errors' => $errors,
      'status' => $status
    );
  }

  /**
   * Validate role.
   *
   * @param \Itk\ApiBundle\Entity\Role $role
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
   */
  public function validateRole(Role $role) {
    $validator = $this->container->get('validator');
    $errors = $validator->validate($role);

    // @todo: What do this magic calculation do?
    $status = count($errors) > 0 ? 400 : 200;

    return array(
      'errors' => $errors,
      'status' => $status
    );
  }

  /**
   * Validate resource.
   *
   * @param \Itk\ApiBundle\Entity\Resource $resource
   *   @TODO Missing description?
   *
   * @return array
   *   @TODO Missing description?
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
   * Validate booking.
   *
   * @param \Itk\ApiBundle\Entity\Booking $booking
   *   @TODO Missing description?
   * @return array
   *   @TODO Missing description?
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
