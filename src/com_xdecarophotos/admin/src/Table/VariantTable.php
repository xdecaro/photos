<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Table\Table;
final class VariantTable extends Table { public function __construct(DatabaseDriver $db) { parent::__construct('#__xdecarophotos_variants','id',$db); } }
