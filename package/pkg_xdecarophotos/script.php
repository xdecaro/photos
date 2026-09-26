<?php
declare(strict_types=1);

use Joomla\CMS\Factory;
use Joomla\CMS\Version;

final class Pkg_XdecarophotosInstallerScript
{
    private const MIN_JOOMLA = '6.1.3';
    private const MIN_CORE = '2.1.0';

    public function preflight(string $type, $parent): bool
    {
        if (version_compare((new Version())->getShortVersion(), self::MIN_JOOMLA, '<')) {
            Factory::getApplication()->enqueueMessage('xdecaro Photos requires Joomla 6.1.3 or later.', 'error');
            return false;
        }
        $coreVersion = $this->detectCoreVersion();
        if ($coreVersion === null || version_compare($coreVersion, self::MIN_CORE, '<')) {
            Factory::getApplication()->enqueueMessage('xdecaro Photos requires Core by xdecaro 2.1.0 or later.', 'error');
            return false;
        }
        return true;
    }

    private function detectCoreVersion(): ?string
    {
        if (defined('JPATH_LIBRARIES')) {
            $candidate = JPATH_LIBRARIES . '/xdecaro/core/VERSION';
            if (is_file($candidate)) return trim((string) file_get_contents($candidate));
        }
        return class_exists('xdecaro\\Core\\Integration\\EntityReference') ? self::MIN_CORE : null;
    }
}
