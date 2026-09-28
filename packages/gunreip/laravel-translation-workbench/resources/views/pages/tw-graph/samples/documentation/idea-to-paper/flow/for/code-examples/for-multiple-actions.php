<?php
$items = loadItems();
$count = count($items);
for ($index = 0; $index < $count; $index++) {
    processItem($items[$index]);
    recordResult($items[$index]);
}
showSummary();
