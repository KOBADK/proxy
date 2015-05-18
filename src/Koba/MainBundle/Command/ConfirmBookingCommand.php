<?php
/**
 * @file
 * Contains the command for ConfirmBookingCommand.
 */
namespace Koba\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ConfirmBookingCommand command.
 *
 * @package Koba\MainBundle\Command
 *
 * @TODO: Implements what interface?
 */
class ConfirmBookingCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this->setName('koba:booking:confirm')
      ->addArgument(
        'id',
        InputArgument::REQUIRED,
        'Which booking entity to confirm?'
      )
      ->setDescription('Send booking to Exchange');
  }

  /**
   * Executes the command
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|null|void
   *
   *
   * @TODO: Find better way to handle last retry. At the moment we only try
   *   maxRetries - 1 times before given up.
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $container = $this->getContainer();
    $doctrine = $container->get('doctrine');
    $em = $doctrine->getManager();

    // Get booking.
    $id = $input->getArgument('id');
    $booking = $doctrine->getRepository('ItkExchangeBundle:Booking')->findOneBy(array('id' => $id));
    if (!$booking) {
      throw new NotFoundHttpException('booking with id:' . $id . ' not found');
    }

    // Check whether this is last retry attempt. Then deny.
    $jobId = $input->getOption('jms-job-id');
    $job = $doctrine->getRepository('JMSJobQueueBundle:Job')->findOneBy(array('id' => $jobId));
    $originalJob = $job->getOriginalJob();
    if ($originalJob) {
      $numberOfRetries = count($originalJob->getRetryJobs());

      $maxRetries = $originalJob->getMaxRetries();

      if ($numberOfRetries >= $maxRetries - 1) {
        $booking->setStatusDenied();
        $em->flush();
        return true;
      }
    }

    // Check Exchange to see if the booking has been accepted.
    $exchangeService = $container->get('itk.exchange_service');
    $accepted = $exchangeService->isBookingAccepted($booking);

    if ($accepted) {
      $booking->setStatusAccepted();
      $em->flush();
      return true;
    }

    // Retry.
    throw new NotFoundHttpException("Not found / accepted. Retry");
  }
}