<?php
declare(strict_types=1);

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\Database\DatabaseInterface;

final class Pkg_XdecarophotosInstallerScript
{
    private const MIN_JOOMLA = '6.1.3';
    private const MIN_CORE = '2.1.0';

    public function preflight(string $type, InstallerAdapter $adapter): bool
    {
        if (version_compare(JVERSION, self::MIN_JOOMLA, '<')) {
            Factory::getApplication()->enqueueMessage(
                'xdecaro Photos richiede Joomla ' . self::MIN_JOOMLA . ' o successivo.',
                'error'
            );
            return false;
        }

        $coreVersion = $this->detectCoreVersion();
        if ($coreVersion === null || version_compare($coreVersion, self::MIN_CORE, '<')) {
            Factory::getApplication()->enqueueMessage(
                'xdecaro Photos richiede Core by xdecaro ' . self::MIN_CORE . ' o successivo.',
                'error'
            );
            return false;
        }

        return true;
    }

    private function detectCoreVersion(): ?string
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select($db->quoteName('manifest_cache'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('package'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('pkg_core'));
        $db->setQuery($query);
        $manifest = $db->loadResult();
        if (!$manifest) {
            return null;
        }
        $decoded = json_decode((string) $manifest, true);
        return is_array($decoded) && isset($decoded['version']) ? (string) $decoded['version'] : null;
    }
}
