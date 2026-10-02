<?php
$items = loadItems();
$index = 0;
while ($index < count($items)) {
    $item = $items[$index];
    $index++;
    if (shouldSkip($item)) {
        continue;
    }
    processItem($item);
}
showSummary();
