<?php
/**
 * @file
 * @TODO: Missing file description?
 */

namespace Itk\WayfBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * This is the class that loads and manages your bundle configuration.
 */
class KobaWayfExtension extends ConfigurableExtension {
  /**
   * {@inheritDoc}
   */
  public function loadInternal(array $configs, ContainerBuilder $container) {
    // Load the bundles service configurations.
    $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
    $loader->load('services.xml');
  }
}