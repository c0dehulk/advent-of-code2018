<?php

$input = file_get_contents(__DIR__ . '/3a.txt');
$strings = explode("\n", trim($input));

$map = [];

foreach ($strings as $string) {
    preg_match('/#(\d+) @ (\d+),(\d+): (\d+)x(\d+)/', trim($string), $matches);
    $claim = [
        'x1' => (int) $matches[2],
        'x2' => (int) $matches[2] + $matches[4],
        'y1' => (int) $matches[3],
        'y2' => (int) $matches[3] + $matches[5],
    ];

    for ($x = $claim['x1']; $x < $claim['x2']; $x++) {
        for ($y = $claim['y1']; $y < $claim['y2']; $y++) {
            $id = $x . ':' . $y;
            $map[$id] = ($map[$id] ?? 0) - 1;
        }
    }
}


$values = array_count_values($map);
echo (count($map) - $values[-1]) . "\n";
