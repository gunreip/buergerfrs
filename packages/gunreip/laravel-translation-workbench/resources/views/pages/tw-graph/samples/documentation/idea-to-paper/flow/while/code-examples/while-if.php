<?php
$items = loadItems();
$index = 0;
while ($index < count($items)) {
    $item = $items[$index];
    if ($item->enabled) {
        processItem($item);
    } else {
        recordSkippedItem($item);
    }
    $index = $index + 1;
}
showSummary();
