<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\MVCComponent;

final class PhotosComponent extends MVCComponent
{
    public function countItems(array $items, string $section): void
    {
        // Joomla dashboard compatibility hook; no category counting is required in v0.1.0.
    }
}
