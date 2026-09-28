const groups = loadGroups();
for (let groupIndex = 0; groupIndex < groups.length; groupIndex++) {
    const items = groups[groupIndex];
    for (let itemIndex = 0; itemIndex < items.length; itemIndex++) {
        processItem(items[itemIndex]);
    }
    const notifications = loadNotifications(groups[groupIndex]);
    for (let notificationIndex = 0; notificationIndex < notifications.length; notificationIndex++) {
        sendNotification(notifications[notificationIndex]);
    }
}
showSummary();
