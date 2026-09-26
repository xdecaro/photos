<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Service;

final class DiagnosticsService
{
    public function __construct(
        private readonly string $componentVersion,
        private readonly string $coreVersion,
        private readonly string $storagePath,
        private readonly array $capabilities,
        private readonly array $imageFormats,
        private readonly string $uploadLimit,
    ) {}

    public static function fromRuntime(string $storagePath): self
    {
        $version = '0.1.0';
        $coreVersion = self::detectCoreVersion();
        $formats = [];
        if (function_exists('imagecreatefromjpeg')) $formats[] = 'jpeg';
        if (function_exists('imagecreatefrompng')) $formats[] = 'png';
        if (function_exists('imagecreatefromwebp')) $formats[] = 'webp';
        return new self(
            $version,
            $coreVersion,
            $storagePath,
            [
                'entity_reference' => class_exists('xdecaro\\Core\\Integration\\EntityReference'),
                'asset_service' => class_exists('xdecaro\\Core\\Asset\\AssetService'),
            ],
            $formats,
            (string) ini_get('upload_max_filesize'),
        );
    }

    public function report(): array
    {
        return [
            'component_version' => $this->componentVersion,
            'package_version' => $this->componentVersion,
            'core_version' => $this->coreVersion,
            'entity_reference' => (bool)($this->capabilities['entity_reference'] ?? false),
            'asset_service' => (bool)($this->capabilities['asset_service'] ?? false),
            'storage_writable' => is_dir($this->storagePath) && is_writable($this->storagePath),
            'image_formats' => array_values($this->imageFormats),
            'php_upload_limit' => $this->uploadLimit,
        ];
    }

    private static function detectCoreVersion(): string
    {
        try {
            $db = \Joomla\CMS\Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
            $q = $db->getQuery(true)->select($db->quoteName('manifest_cache'))->from($db->quoteName('#__extensions'))
                ->where($db->quoteName('type').' = '.$db->quote('package'))
                ->where($db->quoteName('element').' = '.$db->quote('pkg_xdecarocore'));
            $db->setQuery($q); $raw = $db->loadResult(); $data = $raw ? json_decode((string)$raw, true) : null;
            return is_array($data) && isset($data['version']) ? (string)$data['version'] : 'not detected';
        } catch (\Throwable) { return 'not detected'; }
    }
}
