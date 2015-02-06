<?php
/**
 * @file
 * @TODO: Missing file description?
 */

namespace Koba\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 */
class KobaAdminExtension extends Extension {
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
  }
}
