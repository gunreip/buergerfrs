var groups = LoadGroups(); // List<List<Item>>
var groupIndex = 0;
while (groupIndex < groups.Count) {
    var items = groups[groupIndex];
    var itemIndex = 0;
    while (itemIndex < items.Count) {
        ProcessItem(items[itemIndex]);
        itemIndex = itemIndex + 1;
    }
    var notifications = LoadNotifications(groups[groupIndex]);
    var notificationIndex = 0;
    while (notificationIndex < notifications.Count) {
        SendNotification(notifications[notificationIndex]);
        notificationIndex = notificationIndex + 1;
    }
    groupIndex = groupIndex + 1;
}
ShowSummary();
