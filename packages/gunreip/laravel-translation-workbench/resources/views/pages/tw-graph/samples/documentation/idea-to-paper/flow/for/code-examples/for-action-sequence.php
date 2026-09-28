<?php
$groups = loadGroups();
for ($groupIndex = 0; $groupIndex < count($groups); $groupIndex++) {
    $items = $groups[$groupIndex];
    prepareGroup($items);
    for ($itemIndex = 0; $itemIndex < count($items); $itemIndex++) {
        processItem($items[$itemIndex]);
    }
    finalizeGroup($items);
}
showSummary();
