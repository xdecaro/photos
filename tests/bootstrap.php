<?php
declare(strict_types=1);

function fail(string $message): never { fwrite(STDERR, "FAIL: {$message}\n"); exit(1); }
function ok(bool $condition, string $message): void { if (!$condition) fail($message); }
function same(mixed $expected, mixed $actual, string $message): void { if ($expected !== $actual) fail($message . " expected=" . var_export($expected, true) . " actual=" . var_export($actual, true)); }
function contains(string $needle, string $haystack, string $message): void { if (!str_contains($haystack, $needle)) fail($message . " missing={$needle}"); }
function notContains(string $needle, string $haystack, string $message): void { if (str_contains($haystack, $needle)) fail($message . " unexpected={$needle}"); }
function expectException(callable $fn, string $class, string $message): void { try { $fn(); } catch (Throwable $e) { if ($e instanceof $class) return; fail($message . ' wrong exception ' . $e::class . ': ' . $e->getMessage()); } fail($message . ' no exception'); }
