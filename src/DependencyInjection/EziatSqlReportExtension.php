<?php
declare(strict_types = 1);

namespace Eziat\SqlReportBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Tomas
 */
class EziatSqlReportExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor     = new Processor();
        $configuration = new Configuration();
        $loader        = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $config        = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('eziat_sql_report.sql_report_class', $config['sql_report_class']);
        $loader->load('manager.xml');
        $loader->load('controllers.xml');

        $container->setAlias('Eziat\SqlReportBundle\Manager\SqlReportManagerInterface', new Alias('eziat_sql_report.user_manager.default', false));
    }
}