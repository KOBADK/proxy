<?php
/**
 * @file
 * @TODO: Missing file description?
 */

namespace Itk\ApiBundle\EventListeners;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

/**
 * Class KobaExceptionListener
 *
 * @package Itk\ApiBundle\EventListeners
 */
class KobaExceptionListener
{
  /**
   * @TODO Missing function description?
   */
  public function onKernelException(GetResponseForExceptionEvent $event)
  {
    // You get the exception object from the received event
    $exception = $event->getException();

    // If insufficient authentication exception, modify response.
    if ($exception instanceof InsufficientAuthenticationException) {
      $response = new JsonResponse(array('message' => 'unauthorized'), 401);

      // Send the modified response object to the event
      $event->setResponse($response);
    }
  }
}
