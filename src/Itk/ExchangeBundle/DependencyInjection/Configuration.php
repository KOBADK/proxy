<?php
/**
 * @file
 * Load bundle configuration and merge with main config file.
 */
namespace Itk\ExchangeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Itk\ExchangeBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface {
  /**
   * {@inheritDoc}
   */
  public function getConfigTreeBuilder() {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('itk_exchange');

    $rootNode
      ->children()
        ->scalarNode('ws_host')
          ->isRequired()
        ->end()
        ->scalarNode('ws_user')
          ->isRequired()
        ->end()
        ->scalarNode('ws_password')
          ->isRequired()
        ->end()
        ->scalarNode('user_mail')
          ->isRequired()
        ->end()
        ->scalarNode('user_name')
          ->isRequired()
        ->end()
      ->end();

    return $treeBuilder;
  }
}
