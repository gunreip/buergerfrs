<?php
$groups = loadGroups();
for ($groupIndex = 0; $groupIndex < count($groups); $groupIndex++) {
    $items = $groups[$groupIndex];
    for ($itemIndex = 0; $itemIndex < count($items); $itemIndex++) {
        processItem($items[$itemIndex]);
    }
}
showSummary();
