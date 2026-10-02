<?php
function processItems(array $items): array
{
    $events = [];
    try {
        foreach ($items as $index => $valid) {
            if (!$valid) {
                throw new InvalidArgumentException('Invalid item');
            }
            $events[] = 'processed:'.$index;
        }
        $events[] = 'completed';
    } catch (InvalidArgumentException $error) {
        $events[] = 'caught';
    }
    $events[] = 'after';
    return $events;
}
// processItems([true, false, true]) => ['processed:0', 'caught', 'after']
