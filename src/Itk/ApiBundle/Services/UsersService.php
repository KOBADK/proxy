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
 * Class UsersService
 *
 * @package Itk\ApiBundle\Services
 */
class UsersService {
  protected $container;
  protected $doctrine;
  protected $em;

  /**
   * Constructor.
   *
   * @param Container $container
   */
  function __construct(Container $container) {
    $this->container = $container;

    $this->doctrine = $this->container->get('doctrine');
    $this->em = $this->doctrine->getManager();
  }

  /**
   * Get the user with $id
   *
   * @param $id
   * @return array
   */
  public function getUser($id) {
    $user = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User')->findById($id);

    $status = $user ? 200 : 404;

    return array(
      'data' => $user,
      'status' => $status
    );
  }

  /**
   * Get all users
   *
   * @return array
   */
  public function getAllUsers() {
    $users = $this->doctrine->getRepository('Itk\ApiBundle\Entity\User')->findAll();

    $status = $users ? 200 : 404;

    return array(
      'data' => $users,
      'status' => $status
    );
  }
}