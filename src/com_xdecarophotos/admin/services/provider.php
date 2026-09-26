<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use xdecaro\Component\Photos\Administrator\Extension\PhotosComponent;
use xdecaro\Component\Photos\Site\Preset\PresetRegistry;
use xdecaro\Component\Photos\Site\Service\DatabasePhotoRepository;
use xdecaro\Component\Photos\Site\Service\DatabaseVariantRepository;
use xdecaro\Component\Photos\Site\Service\GdImageProcessor;
use xdecaro\Component\Photos\Site\Service\ImageProcessor;
use xdecaro\Component\Photos\Site\Service\PhotoRepository;
use xdecaro\Component\Photos\Site\Service\PhotoService;
use xdecaro\Component\Photos\Site\Service\PhotoStorage;
use xdecaro\Component\Photos\Site\Service\PhotoWorkflowService;
use xdecaro\Component\Photos\Site\Service\UploadValidator;
use xdecaro\Component\Photos\Site\Service\VariantRepository;
use xdecaro\Component\Photos\Site\Service\VariantService;

return new class implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->registerServiceProvider(new MVCFactory('xdecaro\\Component\\Photos'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('xdecaro\\Component\\Photos'));
        $container->share(PhotoRepository::class, static fn(Container $c): PhotoRepository => new DatabasePhotoRepository($c->get(DatabaseInterface::class)));
        $container->share(VariantRepository::class, static fn(Container $c): VariantRepository => new DatabaseVariantRepository($c->get(DatabaseInterface::class)));
        $container->share(PhotoService::class, static fn(Container $c): PhotoService => new PhotoService($c->get(PhotoRepository::class)));
        $container->share(UploadValidator::class, static fn(): UploadValidator => new UploadValidator());
        $container->share(PhotoStorage::class, static fn(): PhotoStorage => new PhotoStorage(JPATH_ROOT . '/images/xdecaro/photos'));
        $container->share(PresetRegistry::class, static fn(): PresetRegistry => new PresetRegistry());
        $container->share(ImageProcessor::class, static fn(): ImageProcessor => new GdImageProcessor());
        $container->share(VariantService::class, static fn(Container $c): VariantService => new VariantService($c->get(PresetRegistry::class),$c->get(ImageProcessor::class),JPATH_ROOT . '/images/xdecaro/photos/variants',$c->get(VariantRepository::class)));
        $container->share(PhotoWorkflowService::class, static fn(Container $c): PhotoWorkflowService => new PhotoWorkflowService($c->get(UploadValidator::class),$c->get(PhotoStorage::class),$c->get(PhotoService::class),$c->get(VariantService::class)));
        $container->share(DiagnosticsService::class, static fn(): DiagnosticsService => new DiagnosticsService('0.1.0',DiagnosticsService::detectCoreVersion(),JPATH_ROOT . '/images/xdecaro/photos'));
        $container->set(ComponentInterface::class, static function (Container $container): ComponentInterface {
            $component = new PhotosComponent($container->get(ComponentDispatcherFactoryInterface::class));
            $component->setMVCFactory($container->get(MVCFactoryInterface::class));
            return $component;
        });
    }
};
