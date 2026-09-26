<?php
function t_assert(bool $condition, string $message): void {
    if (!$condition) { fwrite(STDERR, "FAIL: $message\n"); exit(1); }
}
function t_file(string $path): string {
    t_assert(is_file($path), "missing file $path");
    $c = file_get_contents($path); t_assert($c !== false, "cannot read $path"); return $c;
}
function t_pass(string $name): void { echo "PASS: $name\n"; }
