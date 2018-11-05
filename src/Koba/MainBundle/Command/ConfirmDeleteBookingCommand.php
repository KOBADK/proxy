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
 * Class ConfirmDeleteBookingCommand command.
 *
 * @package Koba\MainBundle\Command
 */
class ConfirmDeleteBookingCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('koba:booking:delete:confirm')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Which booking entity to confirm?'
            )
            ->setDescription('Confirm delete booking in Exchange');
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throw NotFoundHttpException
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        $em = $doctrine->getManager();

        // Get booking.
        $id = $input->getArgument('id');
        $booking = $doctrine->getRepository('ItkExchangeBundle:Booking')
            ->findOneBy(array('id' => $id));
        if (!$booking) {
            throw new NotFoundHttpException(
                'booking with id:'.$id.' not found'
            );
        }

        // Check whether this is last retry attempt. Then deny.
        $jobId = $input->getOption('jms-job-id');
        $job = $doctrine->getRepository('JMSJobQueueBundle:Job')->findOneBy(
            array('id' => $jobId)
        );
        $originalJob = $job->getOriginalJob();
        if ($originalJob && $job->isRetryJob()) {
            $numberOfRetries = count($originalJob->getRetryJobs());

            $maxRetries = $originalJob->getMaxRetries();

            if ($numberOfRetries >= $maxRetries - 1) {
                return;
            }
        }

        // Check Exchange to see if the booking has been accepted.
        $exchangeService = $container->get('itk.exchange_service');
        $accepted = $exchangeService->isBookingAccepted($booking);

        if (!$accepted) {
            $booking->setStatusCancelled();
            $em->flush();

            return;
        }

        // Retry.
        throw new NotFoundHttpException('Booking still exists / Retry.');
    }
}
