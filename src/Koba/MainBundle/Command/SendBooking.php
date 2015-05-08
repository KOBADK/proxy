<?php
/**
 * @file
 * Contains the command for SendBookingCommand.
 */
namespace Koba\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SendBookingCommand command.
 *
 * @package Koba\MainBundle\Command
 */
class SendBookingCommand extends ContainerAwareCommand {
  /**
   * Configure the command
   */
  protected function configure() {
    $this->setName('koba:booking:send')
      ->addArgument('id')
      ->setDescription('Send booking to Exchange');
  }

  /**
   * Executes the command
   *
   * @param InputInterface $input
   * @param OutputInterface $output
   * @return int|null|void
   */
  protected function execute(InputInterface $input, OutputInterface $output) {
    $output->writeln('Sending booking to exchange...');



    $output->writeln('Done.');
  }
}