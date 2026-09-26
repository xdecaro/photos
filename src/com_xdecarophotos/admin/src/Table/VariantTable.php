<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Table;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

final class VariantTable extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__xdecarophotos_variants', 'id', $db);
    }
}
