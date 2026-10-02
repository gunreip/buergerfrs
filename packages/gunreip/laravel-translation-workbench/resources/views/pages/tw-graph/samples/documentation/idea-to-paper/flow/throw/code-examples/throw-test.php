<?php
function processItems(array $items): array
{
    $events = [];
    foreach ($items as $index => $valid) {
        try {
            if (!$valid) {
                throw new InvalidArgumentException('Invalid item');
            }
            $events[] = 'processed:'.$index;
        } catch (InvalidArgumentException $error) {
            $events[] = 'caught:'.$index;
        }
    }
    $events[] = 'completed';
    $events[] = 'after';
    return $events;
}
// [true, false, true] => processed:0, caught:1, processed:2, completed, after
