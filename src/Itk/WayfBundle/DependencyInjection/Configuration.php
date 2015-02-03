<?php
/**
 * @file
 * Load bundle configuration and merge with main config file.
 */
namespace Itk\WayfBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Itk\WayfBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface {
  /**
   * {@inheritDoc}
   */
  public function getConfigTreeBuilder() {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('itk');

    $rootNode
      ->children()
      ->arrayNode('wayf')
      ->children()
      ->scalarNode('certificate')->end()
      ->scalarNode('certificate_key')->end()
      ->end()
      ->end()
      ->end();

    return $treeBuilder;
  }
}
