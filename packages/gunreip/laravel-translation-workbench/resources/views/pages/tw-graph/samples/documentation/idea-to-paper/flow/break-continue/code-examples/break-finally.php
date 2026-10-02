<?php
$items = loadItems();
$index = 0;
while ($index < count($items)) {
    $item = $items[$index];
    try {
        if (mayProcess($item)) {
            processItem($item);
            $index++;
        } else {
            break;
        }
    } finally {
        cleanupItem($item);
    }
}
showSummary();
