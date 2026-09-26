<?php
declare(strict_types=1);
require dirname(__DIR__) . '/bootstrap.php';
$root = dirname(__DIR__, 2);
$sqlPath = $root . '/src/com_xdecarophotos/admin/sql/install.mysql.utf8mb4.sql';
ok(is_file($sqlPath), 'install schema missing');
$sql = file_get_contents($sqlPath);
foreach (['#__xdecarophotos_photos','#__xdecarophotos_variants','owner_component','owner_entity','owner_id','context_component','context_entity','context_id','photo_type','is_primary','photo_id'] as $needle) {
    contains($needle, $sql, 'schema contract');
}
notContains('core_uid', $sql, 'must not add global uid');
contains('INDEX `idx_owner`', $sql, 'owner index');
contains('INDEX `idx_context`', $sql, 'context index');
echo "PASS SchemaContractTest\n";
