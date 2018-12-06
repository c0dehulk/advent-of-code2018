<?php

// Parse the input into an array of points.
$input = trim(file_get_contents(__DIR__ . '/6a.txt'));
$strings = explode("\n", $input);
$points = array_map(
    function ($string) {
        list($x, $y) = explode(',', $string);
        return [(int) $x, (int) $y];
    },
    $strings
);
$ids = 'abcdefghijklmnopqrstuvwxyz';
$ids = str_split($ids . strtoupper($ids));
$ids = array_splice($ids, 0, count($points));
$points = array_combine($ids, $points);

// Find the boundaries of the area to look in.
// We're going to assume that any points outside of these boundaries are effectively infinite.
$minX = null;
$minY = null;
$maxX = null;
$maxY = null;
foreach ($points as $point) {
    $minX = min($point[0], $minX ?: $point[0]);
    $minY = min($point[1], $minY ?: $point[1]);
    $maxX = max($point[0], $maxX ?: $point[0]);
    $maxY = max($point[1], $maxY ?: $point[1]);
}

// Calculate the closest point to each coordinate in our area.
$areas = [];
$infiniteAreas = [];
for ($y = $minY; $y <= $maxY; $y++) {
    for ($x = $minX; $x <= $maxX; $x++) {
        $closestId = '?';
        $closestDistance = PHP_INT_MAX;
        foreach ($points as $id => $point) {
            $distance = abs($x - $point[0]) + abs($y - $point[1]);
            if ($distance < $closestDistance) {
                $closestDistance = $distance;
                $closestId = $id;
            } elseif ($distance === $closestDistance) {
                $closestId = '.';
            }
            // We don't care if the distance is further away than the closest.
        }
        $areas[$closestId] = ($areas[$closestId] ?? 0) + 1;

        // If a bordering coordinate is closest to a point, consider that point infinite.
        if ($x === $minX || $x === $maxX || $y === $minY || $y === $maxY) {
            $infiniteAreas[$closestId] = true;
        }
        //echo $closestId;
    }
    //echo "\n";
}
//echo "\n";

// Filter out all infinite points.
$finiteAreas = [];
foreach ($areas as $id => $area) {
    if (empty($infiniteAreas[$id])) {
        $finiteAreas[$id] = $area;
    }
}

// Output the area with the most closest points.
asort($finiteAreas);
$ids = array_keys($finiteAreas);
$maxId = array_pop($ids);
$maxArea = array_pop($finiteAreas);
echo "$maxId has the largest area with $maxArea.\n";
