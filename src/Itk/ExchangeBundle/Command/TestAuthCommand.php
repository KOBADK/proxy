<?php

namespace Itk\ExchangeBundle\Command;

use Itk\ExchangeBundle\Repository\ResourceRepository;
use Itk\ExchangeBundle\Services\ExchangeWebService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestAuthCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('exchange:auth:get-token')
            ->setDescription('Get an auth token.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $soapClient = $this->getContainer()->get('itk.exchange_soap_client');
        $token = $soapClient->getAuthenticationToken();

        $output->writeln(json_encode($token));
    }
}
