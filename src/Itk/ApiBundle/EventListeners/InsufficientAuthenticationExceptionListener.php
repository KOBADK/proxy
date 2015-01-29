<?php

namespace Itk\ApiBundle\EventListeners;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class InsufficientAuthenticationExceptionListener
{
  public function onKernelException(GetResponseForExceptionEvent $event)
  {
    // You get the exception object from the received event
    $exception = $event->getException();
    $message = sprintf(
      'My Error says: %s with code: %s',
      $exception->getMessage(),
      $exception->getCode()
    );

    // Customize your response object to display the exception details
    $response = new Response();
    $response->setContent($message);

    // HttpExceptionInterface is a special type of exception that
    // holds status code and header details
    if ($exception instanceof InsufficientAuthenticationException) {
      $response = new JsonResponse(array('message' => 'unauthorized'), 401);
    } else {
      $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    // Send the modified response object to the event
    $event->setResponse($response);
  }
}