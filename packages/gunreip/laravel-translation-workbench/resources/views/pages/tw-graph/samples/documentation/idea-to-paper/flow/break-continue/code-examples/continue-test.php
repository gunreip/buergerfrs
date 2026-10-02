<?php
$items = loadItems();
$index = 0;
while ($index < count($items)) {
    $item = $items[$index];
    $index++;
    try {
        if (shouldSkip($item)) {
            continue;
        }
        processItem($item);
    } finally {
        cleanupItem($item);
    }
}
showSummary();
