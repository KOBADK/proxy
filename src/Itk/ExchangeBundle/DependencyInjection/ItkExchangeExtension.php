<?php
/**
 * @file
 * Contains the ItkExchangeExtension.
 */

namespace Itk\ExchangeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 */
class ItkExchangeExtension extends Extension {
  /**
   * {@inheritDoc}
   */
  public function load(array $configs, ContainerBuilder $container) {
    // Parse configuration (config.yml).
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    // Load the bundles service configurations.
    $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
    $loader->load('services.xml');

    // Load services overwrites when in the testing environment.
    $env = $container->getParameter('kernel.environment');
    if ($env === 'test') {
      $loader->load('services_test.xml');
    }

    // Inject the configuration into the service.
    $serviceDefinition = $container->getDefinition('itk.exchange_service');

    // Set host, username and password to exchange web service.
    $serviceDefinition->addMethodCall('initExchangeWebservice', array(
      $config['ws_host'], $config['ws_user'], $config['ws_password']
    ));

    // Set exchange user name and mail.
    $serviceDefinition->addMethodCall('setExchangeUser', array($config['user_name'], $config['user_mail']));
  }
}
