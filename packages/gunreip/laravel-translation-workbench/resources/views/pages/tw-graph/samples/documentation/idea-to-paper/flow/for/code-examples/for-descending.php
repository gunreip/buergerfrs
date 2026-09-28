<?php
$items = loadItems();
$count = count($items);
for ($index = $count - 1; $index >= 0; $index -= 2) {
    processItem($items[$index]);
}
showSummary();
