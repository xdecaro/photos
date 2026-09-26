<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Controller;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Database\DatabaseInterface;
use RuntimeException;
use xdecaro\Component\Photos\Site\Preset\PresetRegistry;
use xdecaro\Component\Photos\Site\Repository\DatabasePhotoRepository;
use xdecaro\Component\Photos\Site\Repository\DatabaseVariantRepository;
use xdecaro\Component\Photos\Site\Service\GdImageProcessor;
use xdecaro\Component\Photos\Site\Service\PhotoService;
use xdecaro\Component\Photos\Site\Service\PhotoStorage;
use xdecaro\Component\Photos\Site\Service\UploadValidator;
use xdecaro\Component\Photos\Site\Service\VariantService;
use xdecaro\Component\Photos\Site\Value\PhotoReference;

final class PhotoController extends BaseController
{
    public function upload(): void
    {
        $this->assertWriteAccess('core.create');

        $file = $this->input->files->get('photo', [], 'array');
        if (!is_array($file) || empty($file['tmp_name']) || !empty($file['error'])) {
            throw new RuntimeException('No valid upload received.');
        }

        $owner = PhotoReference::fromParts(
            $this->input->post->getString('owner_component'),
            $this->input->post->getString('owner_entity'),
            $this->input->post->getString('owner_id'),
        );
        $context = PhotoReference::optionalContext(
            $this->input->post->getString('context_component') ?: null,
            $this->input->post->getString('context_entity') ?: null,
            $this->input->post->getString('context_id') ?: null,
        );

        $params = ComponentHelper::getParams('com_xdecarophotos');
        $maxBytes = max(1, (int) $params->get('max_upload_mb', 12)) * 1024 * 1024;
        $validated = (new UploadValidator($maxBytes))->validate(
            (string) $file['tmp_name'],
            (string) ($file['name'] ?? 'upload'),
            (int) ($file['size'] ?? 0),
        );
        $storage = new PhotoStorage(JPATH_ROOT . '/images/xdecaro/photos');
        $stored = $storage->storeOriginal($validated);

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $service = new PhotoService(new DatabasePhotoRepository($db));
        $record = $service->createPhoto($owner->toEntityReference(), [
            'context' => $context?->toArray(),
            'photo_type' => $this->input->post->getCmd('photo_type', 'original'),
            'title' => $this->input->post->getString('title', ''),
            'season' => $this->input->post->getString('season', '') ?: null,
            'original_path' => $stored->relativePath,
            'mime_type' => $stored->mimeType,
            'width' => $stored->width,
            'height' => $stored->height,
            'file_size' => $stored->size,
            'orientation' => $validated->orientation,
            'created_by' => (int) Factory::getApplication()->getIdentity()->id,
        ]);

        Factory::getApplication()->enqueueMessage('Foto originale caricata. Ora puoi correggere il ritaglio.', 'success');
        $this->setRedirect(Route::_('index.php?option=com_xdecarophotos&view=studio&id=' . $record->id, false));
    }

    public function saveVariant(): void
    {
        $this->assertWriteAccess('core.edit');
        $photoId = $this->input->post->getInt('photo_id');
        if ($photoId < 1) {
            throw new RuntimeException('Photo ID missing.');
        }

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $photos = new PhotoService(new DatabasePhotoRepository($db));
        $photo = $photos->getPhoto($photoId) ?? throw new RuntimeException('Photo not found.', 404);
        $storageRoot = JPATH_ROOT . '/images/xdecaro/photos';
        $storage = new PhotoStorage($storageRoot);
        $source = $storage->absolutePath($photo->originalPath, 'originals');

        $variants = new VariantService(
            new PresetRegistry(),
            new DatabaseVariantRepository($db),
            new GdImageProcessor(),
            $storageRoot . '/variants',
        );
        $variants->createVariantFromPath($photoId, $source, $photo->mimeType, $this->input->post->getCmd('preset', 'avatar'), [
            'crop_x' => $this->input->post->getFloat('crop_x', 0),
            'crop_y' => $this->input->post->getFloat('crop_y', 0),
            'crop_width' => $this->input->post->getFloat('crop_width', 1),
            'crop_height' => $this->input->post->getFloat('crop_height', 1),
            'rotation' => $this->input->post->getInt('rotation', 0),
        ]);

        Factory::getApplication()->enqueueMessage('Variante salvata.', 'success');
        $this->setRedirect(Route::_('index.php?option=com_xdecarophotos&view=gallery', false));
    }

    public function setPrimary(): void
    {
        $this->assertWriteAccess('core.edit');
        $id = $this->input->post->getInt('id');
        if ($id < 1) throw new RuntimeException('Photo ID missing.');
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        (new PhotoService(new DatabasePhotoRepository($db)))->setPrimaryPhoto($id);
        Factory::getApplication()->enqueueMessage('Foto principale aggiornata.', 'success');
        $this->setRedirect(Route::_('index.php?option=com_xdecarophotos&view=gallery', false));
    }

    public function file(): void
    {
        if (!Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_xdecarophotos')) {
            throw new RuntimeException('Not authorised.', 403);
        }
        $id = $this->input->getInt('id');
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $photo = (new PhotoService(new DatabasePhotoRepository($db)))->getPhoto($id) ?? throw new RuntimeException('Photo not found.', 404);
        $storage = new PhotoStorage(JPATH_ROOT . '/images/xdecaro/photos');
        $path = $storage->absolutePath($photo->originalPath, 'originals');
        if (!is_file($path)) throw new RuntimeException('Photo file not found.', 404);
        $app = Factory::getApplication();
        $app->setHeader('Content-Type', $photo->mimeType, true);
        $app->setHeader('Content-Length', (string) filesize($path), true);
        $app->setHeader('X-Content-Type-Options', 'nosniff', true);
        echo file_get_contents($path);
        $app->close();
    }

    private function assertWriteAccess(string $permission): void
    {
        if (!Factory::getApplication()->getIdentity()->authorise($permission, 'com_xdecarophotos')) {
            throw new RuntimeException('Not authorised.', 403);
        }
        if (!Session::checkToken('post')) {
            throw new RuntimeException('Invalid security token.', 403);
        }
    }
}
