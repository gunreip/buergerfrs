<?php
$items = loadItems();
foreach ($items as $item) {
    try {
        processItem($item);
        recordSuccess($item);
    } catch (Throwable $error) {
        handleItemFailure($item, $error);
    }
}
showSummary();
