<?php
$pending = loadPendingItems();
while ($pending !== []) {
    $item = array_shift($pending);
    processItem($item);
}
showSummary();
