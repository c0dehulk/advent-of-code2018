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

// Find the guard who slept the longest.
$highestFrequency = 0;
$highestFrequencyMinute = null;
$highestFrequencyGuardId = null;


/** @var $sleeps DateTimeImmutable[][] */
foreach ($guards as $guardId => $sleeps) {
    $frequencies = [];
    foreach ($sleeps as $sleep) {
        $period = new DatePeriod($sleep[0], new DateInterval('PT1M'), $sleep[1]);
        foreach ($period as $minute) {
            $minute = (int) $minute->format('i');
            $frequencies[$minute] = ($frequencies[$minute] ?? 0) + 1;
        }
    }
    foreach ($frequencies as $minute => $frequency) {

        if ($frequency > $highestFrequency) {
            echo "New highest frequency: {$frequency} for {$guardId} in minute {$minute}\n";
            $highestFrequency = $frequency;
            $highestFrequencyMinute = $minute;
            $highestFrequencyGuardId = $guardId;
        }
    }
}

echo ($highestFrequencyMinute * $highestFrequencyGuardId) . "\n";
