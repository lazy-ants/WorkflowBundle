<?php

namespace Lazyants\WorkflowBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
        $rootNode = $treeBuilder->root('lazyants_workflow');

        $this->addTaskSection($rootNode);
        $this->addWorkflowsSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addTaskSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('tasks')
                    ->defaultValue(array())
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addWorkflowsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('workflows')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('first_step')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('last_step')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('description')->defaultValue('')->end()
                            ->arrayNode('steps')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('task')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('description')->defaultValue('')->end()
                                        ->booleanNode('auto')->defaultFalse()->end()

                                        ->arrayNode('next')
                                            ->performNoDeepMerging()
                                            ->beforeNormalization()->ifString()->then(function($v) { return array('value' => $v); })->end()
                                            ->beforeNormalization()
                                                ->ifTrue(function($v) { return is_array($v) && isset($v['value']); })
                                                ->then(function($v) { return preg_split('/\s*,\s*/', $v['value']); })
                                            ->end()
                                            ->prototype('scalar')->end()
                                        ->end()

                                        ->arrayNode('roles')
                                            ->performNoDeepMerging()
                                            ->beforeNormalization()->ifString()->then(function($v) { return array('value' => $v); })->end()
                                            ->beforeNormalization()
                                                ->ifTrue(function($v) { return is_array($v) && isset($v['value']); })
                                                ->then(function($v) { return preg_split('/\s*,\s*/', $v['value']); })
                                            ->end()
                                            ->prototype('scalar')->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

}
