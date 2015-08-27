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
    $booking = $doctrine->getRepository('ItkExchangeBundle:Booking')
      ->findOneBy(array('id' => $id));
    if (!$booking) {
      throw new NotFoundHttpException('booking with id:' . $id . ' not found');
    }

    // Check whether this is last retry attempt. Then deny.
    $jobId = $input->getOption('jms-job-id');
    $job = $doctrine->getRepository('JMSJobQueueBundle:Job')
      ->findOneBy(array('id' => $jobId));
    $originalJob = $job->getOriginalJob();

    // Confirm that this is a retry job.
    if ($originalJob && $job->isRetryJob()) {
      $numberOfRetries = count($originalJob->getRetryJobs());
      $maxRetries = $originalJob->getMaxRetries();

      // @TODO: Find better way to handle last retry. At the moment we only try maxRetries - 1 times before giving up.
      if ($numberOfRetries >= $maxRetries - 1) {
        $output->writeln('UNCONFIRMED. Last attempt at finding booking in interval returned no elements.');
        $booking->setStatusUnconfirmed();
        $em->flush();
        return;
      }
    }

    // Check Exchange to see if the booking has been accepted.
    $exchangeService = $container->get('itk.exchange_service');
    $exchangeBookings = $exchangeService->getExchangeBookingsForInterval($booking->getResource(), $booking->getStartTime(), $booking->getEndTime());

    // Have we found bookings in interval?
    if (count($exchangeBookings) > 0) {
      // Run through all bookings in interval
      foreach ($exchangeBookings as $exchangeBooking) {
        // Ignore false bookings.
        if (!$exchangeBooking) {
          continue;
        }

        // Is this the booking we are trying to confirm, accept!
        if ($exchangeService->doBookingsMatch($exchangeBooking, $booking)) {
          $booking->setStatusAccepted();
          $em->flush();
          $output->writeln('ACCEPTED.');
          return;
        }
        // If this is not the correct booking, look for overlap with booking
        // we are trying confirm.
        else {
          // Because start and end times between bookings can overlap,
          // we ignore cases where the end of an exchange booking overlaps the
          // start of the current booking, and cases where the start of an
          // exchange booking overlaps the end of the current booking.
          if (($exchangeBooking->getEnd() <= $booking->getStartTime() || $exchangeBooking->getStart() >= $booking->getEndTime())) {
            // If no overlap, ignore.
            continue;
          }
          else {
            // Overlap, booking denied.
            $booking->setStatusDenied();
            $em->flush();
            $output->writeln('REJECTED. Interval booked by other.');
            $this->outputBookings($output, $exchangeBookings, $booking);
            return;
          }
        }
      }
    }

    $this->outputBookings($output, $exchangeBookings, $booking);

    // No bookings. Force retry by throwing exception.
    throw new NotFoundHttpException('RETRY. No bookings found in interval.');
  }

  private function getDateAsString($unixTimestamp) {
    $currentTime = \DateTime::createFromFormat('U', $unixTimestamp);

    return $currentTime->format('c');
  }

  /**
   * Outputs the current booking, and the exchange bookings found in the booking interval to $output
   *
   * @param OutputInterface $output
   *   The output interface.
   * @param $exchangeBookings
   *   The ExchangeBooking(s) found in the interval.
   * @param $booking
   *   The current booking we are trying to confirm.
   */
  private function outputBookings($output, $exchangeBookings, $booking) {
    $output->writeln("----------------");
    $output->writeln("Concerning booking: " . $booking->getSubject() . ": " . $booking->getStartTime() . '(' . $this->getDateAsString($booking->getStartTime()) . ')' . ' to ' . $booking->getEndTime()  . '(' . $this->getDateAsString($booking->getEndTime()) . ')');
    $output->writeln("----------------");
    $output->writeln("Found bookings in interval:");
    foreach ($exchangeBookings as $exchangeBooking) {
      if (!$exchangeBooking) {
        continue;
      }

      $output->write($exchangeBooking->getSubject() . ': ' . $exchangeBooking->getStart() . '(' . $this->getDateAsString($exchangeBooking->getStart()) . ')' . ' to ' . $exchangeBooking->getEnd(). '(' . $this->getDateAsString($exchangeBooking->getEnd()) . ')');

      // Add text to non blocking bookings
      if (($exchangeBooking->getEnd() <= $booking->getStartTime() || $exchangeBooking->getStart() >= $booking->getEndTime())) {
        $output->writeln(' (Not blocking)');
      }
      else {
        $output->writeln('');
      }
    }
  }
}
