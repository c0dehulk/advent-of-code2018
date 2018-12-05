<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

$input = file_get_contents(__DIR__ . '/4a.txt');
$strings = explode("\n", trim($input));
sort($strings);


date_default_timezone_set('UTC');

// Parse all the shifts into an array indexed by guard ID.
$guards = [];
$guardId = 0;
$sleepStart = null;

foreach ($strings as $rowId => $string) {
    preg_match('/\[(.*?)\] (.*)/', $string, $matches);
    $date = new DateTimeImmutable($matches[1]);

    $isWakingUp = ($matches[2] === 'wakes up');
    $isFallingAsleep = ($matches[2] === 'falls asleep');
    $isShiftChange = preg_match('/Guard #(\d+) begins shift/', $matches[2], $bits);
    $newGuardId = (int) ($bits[1] ?? null);

    if ($isShiftChange) {
        // If the current guard is asleep, finish off his shift.
        if ($sleepStart) {
            $guards[$guardId][] = [$sleepStart, $date];
        }

        // Identify the new guard
        preg_match('/Guard #(\d+) begins shift/', $matches[2], $bits);
        $guardId = $newGuardId;
        $guards[$guardId] = ($guards[$guardId] ?? []);
        $sleepStart = null;
        continue;
    }

    if ($isWakingUp) {
        if (!$sleepStart) {
            die("Guard {$guardId} woke up but he's not asleep! Row {$rowId}.\n");
        }
        $guards[$guardId][] = [$sleepStart, $date];
        $sleepStart = null;
        continue;
    }

    if ($isFallingAsleep) {
        if ($sleepStart) {
            die("Guard {$guardId} is already sleeping! Row {$rowId}.\n");
        }
        $sleepStart = $date;
        continue;
    }

    die("Didn't understand row {$rowId}: {$string}\n");
}
ksort($guards);


// Find the guard who slept the longest.
$maxAsleep = 0;
$laziestGuardId = 0;

/** @var $sleeps DateTimeImmutable[][] */
foreach ($guards as $guardId => $sleeps) {
    $total = 0;
    foreach ($sleeps as $sleep) {
        $diff = $sleep[1]->diff($sleep[0], true);
        $minutesAsleep = ($diff->days * 1440) + ($diff->h * 60) + $diff->i;
        $total += $minutesAsleep;
        //echo "Guard {$guardId} slept from {$sleep[0]->format('H:i')} to {$sleep[1]->format('H:i')}, for {$minutesAsleep} minutes.\n";
    }
    if ($total > $maxAsleep) {
        $laziestGuardId = $guardId;
        $maxAsleep = $total;
        //echo "Guard {$guardId} is now the laziest!\n";
    }
}

echo "Laziest guard is {$laziestGuardId}, sleeping for {$maxAsleep} minutes.\n";

// Find the most frequent minute the guard slept.
$frequency = [];
foreach ($guards[$laziestGuardId] as $sleep) {
    $period = new DatePeriod($sleep[0], new DateInterval('PT1M'), $sleep[1]);
    /** @var DateTimeImmutable $minute */
    foreach ($period as $minute) {
        $frequency[$minute->format('i')] = ($frequency[$minute->format('i')] ?? 0) + 1;
    }
}
asort($frequency);
$minutes = array_keys($frequency);
$mostCommonMinute = array_pop($minutes);
echo "Most likely to be asleep in minute {$mostCommonMinute}\n";

$answer = $mostCommonMinute * $laziestGuardId;
echo "Answer is {$answer}\n";
