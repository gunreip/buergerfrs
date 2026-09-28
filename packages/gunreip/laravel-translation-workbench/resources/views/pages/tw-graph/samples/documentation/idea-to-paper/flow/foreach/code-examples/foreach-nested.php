<?php
$groups = loadGroups();
foreach ($groups as $group) {
    foreach ($group as $item) {
        processItem($item);
        recordItemResult($item);
    }
    finalizeGroup($group);
}
showSummary();
