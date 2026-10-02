<?php
function firstPositive(array $groups): int
{
    $groupIndex = 0;
    while ($groupIndex < count($groups)) {
        $group = $groups[$groupIndex];
        $itemIndex = 0;
        while ($itemIndex < count($group)) {
            $item = $group[$itemIndex];
            $itemIndex++;
            if ($item > 0) {
                return $item;
            }
        }
        $groupIndex++;
    }
    return 0;
}
