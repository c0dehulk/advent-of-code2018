<?php

$input = file_get_contents(__DIR__ . '/2a.txt');
$barcodes = explode("\n", trim($input));

$count = count($barcodes);


for ($a = 0; $a < $count; $a++) {
    for ($b = ($a + 1); $b < $count; $b++) {
        $distance = levenshtein($barcodes[$a], $barcodes[$b]);
        if ($distance === 1) {
            echo implode('', array_intersect_assoc(str_split($barcodes[$a]), str_split($barcodes[$b]))) . "\n";
        }
    }
}
