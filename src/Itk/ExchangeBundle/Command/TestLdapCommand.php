<?php

namespace Itk\ExchangeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class TestLdapCommand
 *
 * @package Koba\MainBundle\Command
 */
class TestLdapCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('exchange:ldap:test')
            ->setDescription('Get the ldap resources.');
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
        $ad = $this->getContainer()->get('itk.exchange_ad');
        $resources = $ad->getResources();

        $output->writeln(json_encode($resources));
    }
}
