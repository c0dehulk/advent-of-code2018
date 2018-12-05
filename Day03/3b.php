<?php

$input = file_get_contents(__DIR__ . '/3a.txt');
$strings = explode("\n", trim($input));

// Parse the claims.
$claims = [];
foreach ($strings as $string) {
    preg_match('/#(\d+) @ (\d+),(\d+): (\d+)x(\d+)/', trim($string), $matches);
    $id = (int) $matches[1];
    $claims[$id] = [
        'x1' => (int) $matches[2],
        'x2' => (int) $matches[2] + $matches[4],
        'y1' => (int) $matches[3],
        'y2' => (int) $matches[3] + $matches[5],
    ];
}

// Build the map.
$map = [];
foreach ($claims as $claim) {
    for ($x = $claim['x1']; $x < $claim['x2']; $x++) {
        for ($y = $claim['y1']; $y < $claim['y2']; $y++) {
            $id = $x . ':' . $y;
            $map[$id] = ($map[$id] ?? 0) - 1;
        }
    }
}

// Re-scan all claims to find the one that
foreach ($claims as $id => $claim) {
    for ($x = $claim['x1']; $x < $claim['x2']; $x++) {
        for ($y = $claim['y1']; $y < $claim['y2']; $y++) {
            $square = $x . ':' . $y;
            if ($map[$square] !== -1) {
                continue 3;
            }
        }
    }
    echo $id . "\n";
    exit;
}
