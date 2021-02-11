<?php
/**
 * @file
 * Contains the command for CallbackBookingCommand.
 */

namespace Koba\MainBundle\Command;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CallbackUpdateBookingCommand command.
 *
 * @package Koba\MainBundle\Command
 */
class CallbackUpdateBookingCommand extends ContainerAwareCommand
{
    /**
     * Configure the command
     */
    protected function configure()
    {
        $this->setName('koba:booking:update:callback')
            ->addArgument(
                'id',
                InputArgument::REQUIRED,
                'Which booking entity to update?'
            )
            ->setDescription('Confirm update booking in Exchange');
    }

    /**
     * Executes the command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');

        $id = $input->getArgument('id');
        $booking = $doctrine->getRepository('ItkExchangeBundle:Booking')
            ->findOneBy(array('id' => $id));
        if (!$booking) {
            throw new NotFoundHttpException(
                'booking with id:'.$id.' not found'
            );
        }

        $apiKey = $booking->getApiKey();
        $apiKey = $doctrine->getRepository('KobaMainBundle:ApiKey')->findOneBy(
            array('apiKey' => $apiKey)
        );
        if (!$apiKey) {
            throw new NotFoundHttpException('api key not found');
        }

        $callback = $apiKey->getCallback();

        $client = new Client();

        try {
          $response = $client->post(
            $callback, [
              'query' => [
                '_format' => 'json'
              ],
              'json' => [
                'action' => 'UPDATE',
                'koba_job_id' => $input->getOption('jms-job-id'),
                'status' => $booking->getStatus(),
                'client_booking_id' => $booking->getClientBookingId(),
              ]
            ]
          );

          $output->writeln($response->getStatusCode());
        } catch (\Exception $e) {
            $output->writeln($e->getMessage().' ('.$e->getCode().')');
            throw $e;
        }

        $output->writeln('Callback success');
    }
}
