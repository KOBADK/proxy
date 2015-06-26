<?php
/**
 * @file
 * Contains the command for updating the Calendar events.
 */
namespace Koba\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateCalendarCommand
 *
 * @package Koba\MainBundle\Command
 */
class UpdateCalendarCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this
      ->setName('koba:calendar:update')
      ->setDescription('Update calendar information from XML file.');
  }

  /**
   * Executes the command
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|null|void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $calendarService = $this->getContainer()->get('koba.calendar_service');
    $calendarService->updateXmlData();

    $output->writeln('Calendar events updated.');
  }
}