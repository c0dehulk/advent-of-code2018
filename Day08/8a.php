<?php

// Parse the input.
$input = trim(file_get_contents(__DIR__ . '/8a.txt'));
$numbers = explode(" ", $input);
$numbers = array_map('intval', $numbers);

global $metaDataSum;
$metaDataSum = [];

$nodes = parseNode($numbers);
//print_r($nodes);

echo array_reduce($metaDataSum, function($carry, $value) { return $carry + $value; }, 0) . "\n";




function parseNode($numbers, $id = 0)
{
    $childCount = $numbers[$id];
    $metadataCount = $numbers[$id + 1];

    $children = [];
    $index = $id + 1;
    for ($i = 0; $i < $childCount; $i++) {
        $child = parseNode($numbers, $index + 1);
        $index = $child['end'];
        $children[] = $child;
    }

    if ($metadataCount) {
        $metadata = array_slice($numbers, $index + 1, $metadataCount);
    } else {
        $metadata = [];
    }

    global $metaDataSum;
    $metaDataSum = array_merge($metaDataSum, $metadata);

    return [
        'children' => $children,
        'metadata' => $metadata,
        'start'    => $id,
        'end'      => $index + $metadataCount,
    ];
}
