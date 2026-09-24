<?php
$groups = loadGroups();
$groupIndex = 0;
while ($groupIndex < count($groups)) {
    $items = $groups[$groupIndex];
    prepareGroup($items);
    $itemIndex = 0;
    while ($itemIndex < count($items)) {
        processItem($items[$itemIndex]);
        $itemIndex = $itemIndex + 1;
    }
    finalizeGroup($items);
    $groupIndex = $groupIndex + 1;
}
showSummary();
