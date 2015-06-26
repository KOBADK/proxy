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
   * Tries to find booking in interval:
   * If 0 bookings are found in interval, force retry.
   * If 1 booking is found, make sure it is the correct booking (then accept), else reject.
   * If more than 1 booking is found, reject.
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|null|void
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

      // @TODO: Find better way to handle last retry. At the moment we only try maxRetries - 1 times before giving up.
      if ($numberOfRetries >= $maxRetries - 1) {
        $output->writeln('Last attempt at finding booking in interval returned no elements. Rejected.');
        $booking->setStatusDenied();
        $em->flush();
        return;
      }
    }

    // Check Exchange to see if the booking has been accepted.
    $exchangeService = $container->get('itk.exchange_service');
    $exchangeBookings = $exchangeService->getBookingsForInterval($booking->getResource(), $booking->getStartTime(), $booking->getEndTime());

    // Only one booking in interval.
    if (count($exchangeBookings) === 1) {
      // Is it the correct booking?
      if ($exchangeService->doBookingsMatch($exchangeBookings[0], $booking)) {
        $booking->setStatusAccepted();
        $em->flush();
        $output->writeln('Booking accepted.');
      }
      else {
        $booking->setStatusDenied();
        $em->flush();
        $output->writeln('Booked by other. Rejected.');
      }
    }
    // No bookings. Force retry by throwing exception.
    else if (count($exchangeBookings) === 0) {
      throw new NotFoundHttpException('No booking found in interval. Retry!');
    }
    // More than one booking exists in interval. Therefore it is not $booking = Rejected.
    else {
      $booking->setStatusDenied();
      $em->flush();
      $output->writeln('More than one booking in interval. Rejected.');
    }
  }
}