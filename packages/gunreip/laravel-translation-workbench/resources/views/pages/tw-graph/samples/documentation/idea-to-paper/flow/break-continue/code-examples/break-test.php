<?php
$items = loadItems();
$index = 0;
while ($index < count($items)) {
    $item = $items[$index];
    if (mayProcess($item)) {
        processItem($item);
        $index++;
    } else {
        break;
    }
}
continueProcess();
