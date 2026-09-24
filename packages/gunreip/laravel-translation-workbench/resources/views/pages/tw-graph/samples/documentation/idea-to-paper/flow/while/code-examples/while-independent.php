<?php
$groups = loadGroups();
$groupIndex = 0;
while ($groupIndex < count($groups)) {
    $items = $groups[$groupIndex];
    $itemIndex = 0;
    while ($itemIndex < count($items)) {
        processItem($items[$itemIndex]);
        $itemIndex = $itemIndex + 1;
    }
    $notifications = loadNotifications($groups[$groupIndex]);
    $notificationIndex = 0;
    while ($notificationIndex < count($notifications)) {
        sendNotification($notifications[$notificationIndex]);
        $notificationIndex = $notificationIndex + 1;
    }
    $groupIndex = $groupIndex + 1;
}
showSummary();
