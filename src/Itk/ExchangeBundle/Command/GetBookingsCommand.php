<?php

namespace Itk\ExchangeBundle\Command;

use Itk\ExchangeBundle\Repository\ResourceRepository;
use Itk\ExchangeBundle\Services\ExchangeWebService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GetBookingsCommand
 *
 * @package Koba\MainBundle\Command
 */
class GetBookingsCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('exchange:get:bookings')
            ->addArgument('resource', null, InputOption::VALUE_REQUIRED)
            ->setDescription('Get the booking for resource.');
    }

    /**
     * Executes the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resource = $input->getArgument('resource');

        $container = $this->getContainer();
        $repo = $container->get('itk.exchange_resource_repository');

        $resourceEntity = $repo->findOneBy(array('name' => $resource));
        if (!$resourceEntity) {
            throw new NotFoundHttpException(
                'Resource with name:'.$resource.' not found'
            );
        }

        /** @var ExchangeWebService $webService */
        $webService = $this->getContainer()->get('itk.exchange_web_service');
        $calender = $webService->getRessourceBookings($resourceEntity, time(), strtotime('+1 day'));

        $output->writeln($calender->__toString());
    }
}
