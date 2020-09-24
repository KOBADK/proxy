<?php

namespace Itk\ExchangeBundle\Command;

use Itk\ExchangeBundle\Entity\Booking;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class TestUpdateCommand
 *
 * @package Koba\MainBundle\Command
 */
class TestUpdateCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('exchange:booking:test_update')
            ->addOption('resource', null, InputOption::VALUE_REQUIRED)
            ->setDescription('Creates a test booking, changes the title and deletes the booking again. Confirms that each step is successful.');
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $resourceName = $input->getOption('resource');

        if (!filter_var($resourceName, FILTER_VALIDATE_EMAIL)) {
            $output->writeln("Invalid resource format. Should be an email.");
            return 1;
        }

        $resource = $container->get('itk.exchange_resource_repository')
            ->findOneByMail($resourceName);

        if ($resource === null) {
            $output->writeln("Could not find resource.");
            return 1;
        }

        $exchangeService = $container->get('itk.exchange_service');
        $userName = $container->getParameter(
            'itk_exchange_user_name'
        );
        $mail = $container->getParameter('itk_exchange_user_mail');

        // Step 1. Create booking.

        $output->writeln('Step 1. Creating booking.');

        $from = time() + 1800;
        $to = $from + 1800;
        $title = 'Test meeting ' . $from;

        // Create a test booking.
        $booking = new Booking();
        $booking->setSubject($title);
        $booking->setDescription(
            'Description of the meeting.'
        );
        $booking->setName($userName);
        $booking->setMail($mail);
        $booking->setStartTime($from);
        $booking->setEndTime($to);
        $booking->setResource($resource);
        $booking->setStatusPending();

        $exchangeService->createBooking($booking);

        $this->wait($output);

        $output->writeln('Finding booking in exchange...');

        $exchangeBookingFound = null;

        $calendar = $exchangeService->getResourceBookings($resource, $from, $to);
        $exchangeBookings = $calendar->getBookings();

        /** @var \Itk\ExchangeBundle\Model\ExchangeBooking $exchangeBooking */
        foreach ($exchangeBookings as $exchangeBooking) {
            if ($exchangeBooking->getSubject() === $title) {
                $exchangeBookingFound = $exchangeBooking;
                break;
            }
        }

        if ($exchangeBookingFound === null) {
            $output->writeln("Could not find exchange booking.");
            return 1;
        }

        // Step 2. Update booking.

        $output->writeln('Step 2. Updating booking.');

        $title2 = $title . ' 2';

        $booking->setName($title2);

        $exchangeService->updateBooking($booking);

        $this->wait($output);

        $output->writeln('Finding booking in exchange...');

        $calendar = $exchangeService->getResourceBookings($resource, $from, $to);
        $exchangeBookings = $calendar->getBookings();

        $exchangeBookingFound = null;

        /** @var \Itk\ExchangeBundle\Model\ExchangeBooking $exchangeBooking */
        foreach ($exchangeBookings as $exchangeBooking) {
            if ($exchangeBooking->getSubject() === $title2) {
                $exchangeBookingFound = $exchangeBooking;
                break;
            }
            else if ($exchangeBooking->getSubject() === $title) {
                $output->writeln('Booking did not change title.');
                return 1;
            }
        }

        if ($exchangeBookingFound === null) {
            $output->writeln("Could not find exchange booking.");
            return 1;
        }

        // Step 3. Delete booking

        $output->writeln('Step 3. Cancelling booking');

        $exchangeService->cancelBooking($booking);

        $this->wait($output);

        $output->writeln('Confirming booking is removed from exchange...');

        $calendar = $exchangeService->getResourceBookings($resource, $from, $to);
        $exchangeBookings = $calendar->getBookings();

        $exchangeBookingFound = null;

        /** @var \Itk\ExchangeBundle\Model\ExchangeBooking $exchangeBooking */
        foreach ($exchangeBookings as $exchangeBooking) {
            if ($exchangeBooking->getSubject() === $title2) {
                $exchangeBookingFound = $exchangeBooking;
                break;
            }
        }

        if ($exchangeBookingFound !== null) {
            $output->writeln('Booking not removed from exchange');
            return 1;
        }

        $output->writeln('Test successfully completed.');

        return 0;
    }

    private function wait($output) {
        $output->writeln('Waiting 10 secs');
        for ($i = 0; $i < 5; $i++) {
            sleep(1);
            $output->write('.');
        }

        $output->writeln('');
    }
}
