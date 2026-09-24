const groups = loadGroups();
let groupIndex = 0;
while (groupIndex < groups.length) {
    const items = groups[groupIndex];
    let itemIndex = 0;
    while (itemIndex < items.length) {
        processItem(items[itemIndex]);
        itemIndex = itemIndex + 1;
    }
    const notifications = loadNotifications(groups[groupIndex]);
    let notificationIndex = 0;
    while (notificationIndex < notifications.length) {
        sendNotification(notifications[notificationIndex]);
        notificationIndex = notificationIndex + 1;
    }
    groupIndex = groupIndex + 1;
}
showSummary();
