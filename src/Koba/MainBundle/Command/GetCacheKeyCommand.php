<?php

namespace Koba\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class GetCacheKeyCommand
 *
 * @package Koba\MainBundle\Command
 */
class GetCacheKeyCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this
            ->setName('koba:cache:get')
            ->addArgument(
                'key',
                InputArgument::REQUIRED,
                'Which key should we get?'
            )
            ->setDescription('Get the value of a cache key.');
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
        $calendarService = $this->getContainer()->get('koba.calendar_service');
        $entry = $calendarService->getCacheKey($input->getArgument('key'));

        $output->writeln($entry);
    }
}
