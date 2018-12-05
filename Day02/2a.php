<?php

$input = file_get_contents(__DIR__ . '/2a.txt');
$barcodes = explode("\n", trim($input));

$twos = 0;
$threes = 0;
foreach ($barcodes as $barcode) {
    $letters = countLetters($barcode);
    $twos += (in_array(2, $letters, true) ? 1 : 0);
    $threes += (in_array(3, $letters, true) ? 1 : 0);
}
echo ($twos * $threes) . "\n";


function countLetters(string $barcode)
{
    $letters = [];
    $chars = str_split($barcode);
    foreach ($chars as $char) {
        $letters[$char] = ($letters[$char] ?? 0) + 1;
    }
    return $letters;
}
