<?php

// Parse the input into an array of input strings.
$input = trim(file_get_contents(__DIR__ . '/7a.txt'));
$strings = explode("\n", $input);

// Parse the strings into a map of steps against dependencies.
$steps = [];
foreach ($strings as $string) {
    preg_match('/Step ([a-z]) must be finished before step ([a-z]) can begin./i', $string, $matches);
    if (!array_key_exists($matches[2], $steps)) {
        $steps[$matches[2]] = [];
    }
    if (!array_key_exists($matches[1], $steps)) {
        $steps[$matches[1]] = [];
    }
    $steps[$matches[2]][] = $matches[1];
    sort($steps[$matches[2]]);
}
ksort($steps);

// Resolve the steps in alphabetical order.
$resolved = [];
$toResolve = $steps;
reset($toResolve);
while (count($resolved) !== count($steps)) {
    $dependencies = current($toResolve);
    $step = key($toResolve);

    // If we can't resolve a step, ignore it.
    if (array_diff($dependencies, $resolved)) {
        next($toResolve);
        continue;
    }

    // If we can resolve a step, remove it from the pending list and restart from the beginning.
    $resolved[] = $step;
    unset($toResolve[$step]);
    reset($toResolve);
}

echo implode('', $resolved) . "\n";
