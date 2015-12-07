<?php

namespace Lcn\XRobotsTagBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('lcn_x_robots_tag');

        $rootNode
            ->children()
                ->booleanNode('enabled')->defaultFalse()->end()
                ->arrayNode('rules')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('default')
                            ->addDefaultsIfNotSet()
                            ->beforeNormalization()
                                ->ifTrue()
                                ->then(function ($v) { return ['noindex' => $v, 'nofollow' => $v]; })
                            ->end()
                            ->children()
                                ->scalarNode('noindex')->defaultFalse()->end()
                                ->scalarNode('nofollow')->defaultFalse()->end()
                            ->end()
                        ->end()
                        ->arrayNode('user_roles')
                            ->beforeNormalization()
                                ->ifTrue()
                                ->then(function ($v) { return ['*' => ['noindex' => $v, 'nofollow' => $v]]; })
                            ->end()
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->beforeNormalization()
                                    ->ifTrue()
                                    ->then(function ($v) { return ['noindex' => $v, 'nofollow' => $v]; })
                                ->end()
                                ->children()
                                    ->scalarNode('noindex')->defaultFalse()->end()
                                    ->scalarNode('nofollow')->defaultFalse()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
