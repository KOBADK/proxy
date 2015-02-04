<?php
/**
 * @file
 * Load bundle configuration and merge with main config file.
 */
namespace Itk\WayfBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;

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
    $rootNode = $treeBuilder->root('itk_wayf');

    // Try to load the self sign test certificate.
    $sslDirectories = array(__DIR__ . '/../Resources/ssl');
    $locator = new FileLocator($sslDirectories);
    $certificate = $locator->locate('selfSigned.cert', NULL, TRUE);
    $key = $locator->locate('selfSigned.key', NULL, TRUE);

    // Try to build default asc (HACK).
    $sp = ($_SERVER['SERVER_PORT'] == 80 ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'];
    $asc =  $sp . $_SERVER['REDIRECT_URL'];

    $rootNode
      ->children()
        ->enumNode('mode')
          ->values(array('test', 'qa', 'production'))
          ->defaultValue('test')
          ->info('Defines the end-point to connect to at WAYF and which mode we are operating.')
        ->end()
        ->arrayNode('idp')
          ->addDefaultsIfNotSet()
          ->children()
            ->scalarNode('asc')
              ->defaultValue($asc)
              ->info('The AssertionConsumerService URL where wayf POST user information back to us (wayf/login).')
            ->end()
            ->scalarNode('sp')
              ->defaultValue($sp)
              ->info('The Service Provider (SP) normally the site base URL.')
            ->end()
          ->end()
        ->end()
        ->arrayNode('certificate')
          ->addDefaultsIfNotSet()
          ->children()
            ->scalarNode('cert')
              ->defaultValue($certificate)
              ->info('The SSL certificate used to generate metadata at WAYF.dk.')
            ->end()
            ->scalarNode('key')
              ->defaultValue($key)
              ->info('The private SSL key used to sign the certificate.')
            ->end()
          ->end()
        ->end()
      ->end();

    return $treeBuilder;
  }
}
