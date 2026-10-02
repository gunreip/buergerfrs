<?php
$items = loadItems();
for ($index = 0; $index < count($items); $index++) {
    $item = $items[$index];
    if (shouldSkip($item)) {
        continue;
    }
    processItem($item);
}
showSummary();
