<?php

$input = trim(file_get_contents(__DIR__ . '/5a.txt'));


$shortest = PHP_INT_MAX;
for($i = 65; $i <= 90; $i++) {
    $polymer = str_replace(chr($i), '', $input);
    $polymer = str_replace(chr($i + 32), '', $polymer);
    $length = react($polymer);
    $shortest = min($length, $shortest);
}
echo $shortest . "\n";

function react($polymer)
{
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
    return strlen($polymer);
}

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
