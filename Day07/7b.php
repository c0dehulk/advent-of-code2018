<?php

// Parse the input into an array of input strings.
$input = trim(file_get_contents(__DIR__ . '/7a.txt'));
$strings = explode("\n", $input);

// Parse the strings into a map of steps against dependencies.
$steps = [];
foreach ($strings as $string) {
    preg_match('/Step ([a-z]) must be finished before step ([a-z]) can begin./i', $string, $matches);
    if (!array_key_exists($matches[2], $steps)) {
        $steps[$matches[2]] = [];
    }
    if (!array_key_exists($matches[1], $steps)) {
        $steps[$matches[1]] = [];
    }
    $steps[$matches[2]][] = $matches[1];
    sort($steps[$matches[2]]);
}
ksort($steps);


// Resolve the steps in alphabetical order.
$workers = array_fill(0, 5, [0, null]);

$resolved = [];
$toResolve = $steps;

echo "Time  ";
foreach ($workers as $workerId => $state) { echo str_pad($workerId + 1, 3); }
echo "Done\n";

$t = 0;
while (true) {
//    echo "# Woke up at $t\n";

    // Find available workers.
    $availableIds = [];
    foreach ($workers as $workerId => $state) {
        if ($state[0] <= $t) {
            $availableIds[] = $workerId;
            if (!empty($state[1])) {
                $resolved[] = $state[1];
                $workers[$workerId] = [$state[0], null];
            }
        }
    }
//    echo "#" . implode(',', $availableIds) . "\n";

    // Assign available workers.
    foreach ($availableIds as $workerId) {

//        echo "#" . count($toResolve) . " jobs remaining to resolve\n";

        $nextStep = null;
        foreach ($toResolve as $step => $dependencies) {
            if (!array_diff($dependencies, $resolved)) {
                $nextStep = $step;
                break;
            }
        }

        if (!$nextStep) {
//            echo "# Can't resolve anything else at this time\n";
            break;
        }

        // If we can resolve a step, get a worker started.
        $completionTime = (ord($nextStep) - 64) + $t + 60;
        $workers[$workerId] = [$completionTime, $nextStep];

//        echo "# Starting on {$nextStep}. Will complete at {$completionTime}.\n";

        // Remove the step from our TTD list.
        unset($toResolve[$nextStep]);
    }

    echo str_pad($t, 6);
    foreach ($workers as $id => $state) {
        echo ($state[1] ?? '.') . '  ';
    }
    echo implode('', $resolved) . "\n";

    if (count($resolved) === count($steps)) {
        echo $t . "\n";
        die();
    }

    $t++;
}
die("escaped\n");
