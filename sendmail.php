#!/usr/bin/php
<?php

$log_output = '';
$handle = fopen('php://stdin', 'rb');

while (!feof($handle)) {
    $log_output .= fgets($handle);
}

$mailDir = __DIR__ . '/var';

if (!is_readable($mailDir) && !mkdir($mailDir, 0775, true) && !is_dir($mailDir)) {
    throw new \RuntimeException(sprintf('Directory "%s" was not created', $mailDir));
}

$mailPath = $mailDir . '/' . date('Ymd-His') . substr(microtime(true), 1, 5) . '.txt';
file_put_contents($mailPath, $log_output);