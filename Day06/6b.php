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

// Pad our boundaries, because... my gut says it won't work if you don't.
$minX -= 100;
$minY -= 100;
$maxX += 100;
$maxY += 100;

// Calculate the distance to all points for each coordinate in our area.
$area = 0;
for ($y = $minY; $y <= $maxY; $y++) {
    for ($x = $minX; $x <= $maxX; $x++) {

        $totalDistance = 0;
        foreach ($points as $id => $point) {
            $distance = abs($x - $point[0]) + abs($y - $point[1]);
            $totalDistance += $distance;
        }

        // This assumes the region we're searching for is contiguous.
        if ($totalDistance < 10000) {
            $area++;
        }
    }
}

// Output the area with the most closest points.
echo "Region less than 10000 units away from all points is $area big.\n";
