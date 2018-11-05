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
 * Class DeleteBookingCommand command.
 *
 * @package Koba\MainBundle\Command
 */
class DeleteBookingCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('koba:booking:delete')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Which booking entity to delete?'
            )
            ->setDescription('Remove booking from Exchange');
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

        // Get booking.
        $id = $input->getArgument('id');
        $booking = $doctrine->getRepository('ItkExchangeBundle:Booking')
            ->findOneBy(array('id' => $id));
        if (!$booking) {
            throw new NotFoundHttpException(
                'booking with id:'.$id.' not found'
            );
        }

        // Check Exchange to see if the booking has been accepted.
        $exchangeService = $container->get('itk.exchange_service');

        $exchangeService->cancelBooking($booking);
    }
}
