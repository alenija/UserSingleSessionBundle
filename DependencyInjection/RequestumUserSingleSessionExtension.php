<?php

namespace Requestum\UserSingleSessionBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class RequestumUserSingleSessionExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->settingUpStorage($config['storage'], $container);
        $this->settingUpFailureAction($config['failure_action'], $container);
    }

    private function settingUpStorage($manager, ContainerBuilder $builder)
    {
        $definition = $builder->findDefinition('requestum_user_single_session.validator');
        switch ($manager) {
            case 'memcached':
                $manager = sprintf('requestum_user_single_session.token_manager.%s', $manager);
                break;
            case 'entity':
                $manager = sprintf('requestum_user_single_session.token_manager.%s', $manager);
                break;
        }

        $definition->addArgument(new Reference($manager));
    }

    private function settingUpFailureAction($handler, ContainerBuilder $builder)
    {
        $definition = $builder->findDefinition('requestum_user_single_session.listener');

        if(!array_key_exists('type', $handler)){
            throw new InvalidConfigurationException(
                'Option `type:` in requestum_user_single_session is required.'
            );
        }

        switch ($handler['type']) {
            case 'logout':
                $handler = sprintf('requestum_user_single_session.validation_failure_handler.%s', $handler['type']);
                break;
            case 'view':
                $this->settingUpViewFailureHandler($handler['template'], $builder);
                $handler = sprintf('requestum_user_single_session.validation_failure_handler.%s', $handler['type']);
                break;
            default:
                $handler = sprintf('requestum_user_single_session.validation_failure_handler.%s', $handler['type']);
                break;
        }

        $definition->addArgument(new Reference($handler));
    }

    private function settingUpViewFailureHandler($view, ContainerBuilder $builder)
    {
        if(!$view){
            throw new InvalidConfigurationException(
                'Value for `template:` in requestum_user_single_session is required.'
            );
        }

        $definition = $builder->findDefinition('requestum_user_single_session.validation_failure_handler.view');

        $definition->addArgument($view);
    }
}
