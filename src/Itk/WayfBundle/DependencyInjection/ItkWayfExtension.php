<?php
/**
 * @file
 * @TODO: Missing file description?
 */

namespace Itk\WayfBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 */
class ItkWayfExtension extends Extension {
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

    // Inject the configuration into the service.
    $serviceDefintion = $container->getDefinition('itk.wayf_service');

    // Set certificates.
    $serviceDefintion->addMethodCall('setCertificateInformation', array($config['certificate']['cert'], $config['certificate']['key']));

    // Set operation mode.
    $serviceDefintion->addMethodCall('setIdpMode', array($config['mode']));

    // Set AssertionConsumerService (acs).
    $serviceDefintion->addMethodCall('setAssertionConsumerService', array($config['idp']['asc']));

    // Set Service Provider ID (site url).
    $serviceDefintion->addMethodCall('setServiceProvicer', array($config['idp']['sp']));
  }
}
