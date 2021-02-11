<?php
/**
 * @file
 * Contains the command for SendBookingCommand.
 */

namespace Koba\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UpdateBookingCommand command.
 *
 * @package Koba\MainBundle\Command
 */
class UpdateBookingCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('koba:booking:update:send')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Which booking entity to update?'
            )
            ->setDescription('Update booking in Exchange');
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

        $id = $input->getArgument('id');

        $booking = $doctrine->getRepository('ItkExchangeBundle:Booking')
            ->findOneBy(array('id' => $id));

        if (!$booking) {
            throw new NotFoundHttpException(
                'booking with id:'.$id.' not found'
            );
        }

        $exchangeService = $container->get('itk.exchange_service');
        $exchangeService->updateBooking($booking);

        $booking->setStatusPending();
        $em->flush();

        $output->writeln('Booking update sent.');
    }
}
