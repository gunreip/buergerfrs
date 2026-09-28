<?php
$items = loadItems();
$count = count($items);
for ($index = 0; $index < $count; $index++) {
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
}
showSummary();
