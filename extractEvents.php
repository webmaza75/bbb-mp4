<?php

/**
 * @use php extractCursorEvents.php --events-file-src=events.xml --events-file-dst=events.new.xml > cursor.events
 */

$options = getopt('', ['events-file-src:', 'events-file-dst:']);
$eventsFileName = realpath($options['events-file-src']);
$newFileName    = $options['events-file-dst'];

$s = fopen($eventsFileName, 'r');
$d = fopen($newFileName, 'w');
$passthru = true;
$captured = false;
$buffer = [];
while (false !== $line = fgets($s, 10240) ) {

    if (preg_match('~<event\s+timestamp="(\d+)".+eventname="CursorMoveEvent">~', $line, $m)) {
        $timestamp = $m[1];
        $passthru = false;
        $captured = true;
        $buffer[0] = $timestamp;
        continue;
    }

    if ($captured) {
        if (preg_match('~<xOffset>([\d\.]+)</xOffset>~', $line, $m)) {
            $buffer[1] = $m[1];
            continue;
        }
        if (preg_match('~<yOffset>([\d\.]+)</yOffset>~', $line, $m)) {
            $buffer[2] = $m[1];
            continue;
        }

        if (preg_match('~</event>~', $line)) {
            ksort($buffer);
            echo implode(', ', $buffer) . "\n";
            $buffer = [];
            $captured = false;
            $passthru = true;
            continue;
        }
    }

    if ($passthru) {
        fwrite($d, $line);
    }
}
fclose($s);
fclose($d);