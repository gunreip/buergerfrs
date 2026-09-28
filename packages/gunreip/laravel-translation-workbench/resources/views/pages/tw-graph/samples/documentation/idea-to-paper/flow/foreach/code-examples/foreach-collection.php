<?php
$items = loadItems();
foreach ($items as $item) {
    processItem($item);
}
showSummary();
