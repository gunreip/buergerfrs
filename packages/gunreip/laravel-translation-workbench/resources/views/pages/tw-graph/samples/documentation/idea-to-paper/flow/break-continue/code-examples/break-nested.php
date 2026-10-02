<?php
$groups = loadGroups();
$groupIndex = 0;
while ($groupIndex < count($groups)) {
    $items = $groups[$groupIndex];
    $itemIndex = 0;
    while ($itemIndex < count($items)) {
        $item = $items[$itemIndex];
        $itemIndex++;
        if (!mayProcess($item)) {
            break; // Only the inner WHILE.
        }
        processItem($item);
    }
    $groupIndex++;
}
showSummary();
