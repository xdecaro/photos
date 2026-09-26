<?php
declare(strict_types=1);
namespace xdecaro\Core\Integration {
    final class EntityReference {
        public function __construct(public string $component, public string $entity, public string $id) {}
    }
}
namespace {
require dirname(__DIR__) . '/bootstrap.php';
require dirname(__DIR__, 2) . '/src/com_xdecarophotos/site/src/Value/PhotoReference.php';
use xdecaro\Component\Photos\Site\Value\PhotoReference;

foreach ([['', 'person', '1'], ['com_xdecaropeople', '', '1'], ['com_xdecaropeople','person','']] as $bad) {
    expectException(fn() => PhotoReference::fromParts(...$bad), \InvalidArgumentException::class, 'invalid owner reference');
}
$ref = PhotoReference::fromParts('com_xdecaropeople', 'person', '125');
same('125', $ref->id(), 'id must remain string');
$core = $ref->toEntityReference();
same('com_xdecaropeople', $core->component, 'component');
same('person', $core->entity, 'entity');
same('125', $core->id, 'core id');
expectException(fn() => PhotoReference::optionalContext('com_xdecarocompetitions', null, '2'), \InvalidArgumentException::class, 'partial context');
same(null, PhotoReference::optionalContext(null, null, null), 'empty context');
echo "PASS PhotoReferenceTest\n";
}
