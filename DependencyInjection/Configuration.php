<?php

namespace Kit\CryptBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{

    /**
     *
     * {@inheritdoc}
     *
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kit_crypt');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->children()
            ->arrayNode('clients')
            ->useAttributeAsKey('name')
            ->arrayPrototype()
            ->children()
            ->scalarNode('secret_key')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('secret_iv')
            ->end()
            ->scalarNode('method')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('option')
            ->cannotBeEmpty()
            ->end()
            ->end()
            ->end()
            ->end();
        return $treeBuilder;
    }
}
