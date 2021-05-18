<?php

namespace Itk\ExchangeBundle\Command;

use Itk\ExchangeBundle\Services\ExchangeService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RefreshResourcesCommand
 *
 * @package Koba\MainBundle\Command
 */
class RefreshResourcesCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('exchange:ldap:resources')
            ->setDescription('Get resources from ldap.');
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
        /** @var ExchangeService $exchangeService */
        $exchangeService = $this->getContainer()->get('itk.exchange_service');
        $exchangeService->refreshResources();

        $output->writeln("Resources refreshed.");
    }
}
