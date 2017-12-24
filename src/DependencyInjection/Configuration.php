<?php
declare(strict_types = 1);

namespace Eziat\SqlReportBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Tomas
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('eziat_sql_report');

        $rootNode
            ->children()
                ->scalarNode('sql_report_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()
            ->end();

        return $treeBuilder;
    }
}