<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use xdecaro\Component\Photos\Administrator\Extension\PhotosComponent;

return new class implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('xdecaro\\Component\\Photos'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('xdecaro\\Component\\Photos'));
        $container->set(ComponentInterface::class, static function (Container $container): ComponentInterface {
            $component = new PhotosComponent($container->get(ComponentDispatcherFactoryInterface::class));
            $component->setMVCFactory($container->get(MVCFactoryInterface::class));
            return $component;
        });
    }
};
