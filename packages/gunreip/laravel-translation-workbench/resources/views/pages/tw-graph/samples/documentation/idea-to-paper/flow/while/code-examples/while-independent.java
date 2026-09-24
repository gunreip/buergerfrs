List<List<Item>> groups = loadGroups();
int groupIndex = 0;
while (groupIndex < groups.size()) {
    List<Item> items = groups.get(groupIndex);
    int itemIndex = 0;
    while (itemIndex < items.size()) {
        processItem(items.get(itemIndex));
        itemIndex = itemIndex + 1;
    }
    List<Notification> notifications = loadNotifications(groups.get(groupIndex));
    int notificationIndex = 0;
    while (notificationIndex < notifications.size()) {
        sendNotification(notifications.get(notificationIndex));
        notificationIndex = notificationIndex + 1;
    }
    groupIndex = groupIndex + 1;
}
showSummary();
