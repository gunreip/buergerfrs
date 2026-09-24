<?php
$items = loadItems();
$index = 0;
while ($index < count($items)) {
    processItem($items[$index]);
    $index = $index + 1;
}
showSummary();
