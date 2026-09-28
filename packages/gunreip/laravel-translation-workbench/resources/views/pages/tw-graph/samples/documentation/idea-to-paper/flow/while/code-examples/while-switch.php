<?php
$items = loadItems();
$index = 0;
while ($index < count($items)) {
    $item = $items[$index];
    switch ($item->status) {
        case "draft":
            editDraft($item);
            break;
        case "published":
            displayItem($item);
            break;
        default:
            recordUnknownStatus($item);
            break;
    }
    $index = $index + 1;
}
showSummary();
