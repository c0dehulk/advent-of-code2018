<?php

// Parse the input.
$input = trim(file_get_contents(__DIR__ . '/8a.txt'));
$numbers = explode(" ", $input);
$numbers = array_map('intval', $numbers);

$node = parseNode($numbers);
echo $node['value'] . "\n";


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


    if (!$childCount) {
        $value = array_reduce(
            $metadata,
            function ($carry, $value) {
                return $carry + $value;
            },
            0
        );
    } else {
        $value = 0;
        foreach ($metadata as $childId) {
            $childId -= 1;
            $value += ($children[$childId]['value'] ?? 0);
        }
    }

    return [
        'children' => $children,
        'metadata' => $metadata,
        'start'    => $id,
        'end'      => $index + $metadataCount,
        'value'    => $value,
    ];
}
