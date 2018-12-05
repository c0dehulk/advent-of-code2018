<?php

$input = trim(file_get_contents(__DIR__ . '/5a.txt'));

$polymer = $input;
$lastI = strlen($polymer);

for ($i = 0; $i < $lastI; $i++) {
    $a = $polymer[$i];
    $b = $polymer[$i + 1];

    if (!isReactive($a, $b)) {
        continue;
    }

    $polymer = slice($polymer, $i, 2);
    $lastI = strlen($polymer) - 1;
    $i = max(-1, $i - 2);
}
echo strlen($polymer) . "\n";


function isReactive($a, $b)
{
    $aPolarity = (ord($a) < 91);
    $bPolarity = (ord($b) < 91);
    return (strtolower($a) === strtolower($b)) && ($aPolarity !== $bPolarity);
}

function slice($string, $start, $length)
{
    return substr($string, 0, $start) . substr($string, $start + $length);
}
