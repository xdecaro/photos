<?php
declare(strict_types=1);
namespace xdecaro\Component\Photos\Administrator\Table;
use Joomla\Database\DatabaseDriver;
use Joomla\CMS\Table\Table;
final class PhotoTable extends Table { public function __construct(DatabaseDriver $db) { parent::__construct('#__xdecarophotos_photos','id',$db); } }
