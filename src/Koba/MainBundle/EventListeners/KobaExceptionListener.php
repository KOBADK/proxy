<?php
/**
 * @file
 * Contains the Koba exception listener.
 */

namespace Koba\MainBundle\EventListeners;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

/**
 * Class KobaExceptionListener
 *
 * @package Itk\ApiBundle\EventListeners
 */
class KobaExceptionListener {
  protected $kernel;

  /**
   * Constructor.
   *
   * @param $kernel
   *   The Kernel
   */
  public function __construct($kernel) {
    $this->kernel = $kernel;
  }

  /**
   * Event listener for exceptions.
   *
   * Changes how exceptions are presented by KOBA.
   * Hides stack traces for prod env.
   *
   * @TODO: Not implemented!
   *
   * @param GetResponseForExceptionEvent $event
   */
  public function onKernelException(GetResponseForExceptionEvent $event) {
    // You get the exception object from the received event
    $exception = $event->getException();

    $env = $this->kernel->getEnvironment();

    // In a dev environment, show complete exception.
    if ($env === 'test') {
      $exception = $event->getException();
      $statusCode = $exception->getCode() === 0 ? 500 : $exception->getCode();

      $response = new JsonResponse(array('message' => $exception->getMessage(), 'stack_trace' => $exception->getTrace()), $statusCode);

      $event->setResponse($response);
      return;
    }
    else {
      // If insufficient authentication exception, modify response.
      if ($exception instanceof InsufficientAuthenticationException) {
        $response = new JsonResponse(array('message' => 'unauthorized'), 401);

        // Send the modified response object to the event
        $event->setResponse($response);
      }
    }
  }
}
