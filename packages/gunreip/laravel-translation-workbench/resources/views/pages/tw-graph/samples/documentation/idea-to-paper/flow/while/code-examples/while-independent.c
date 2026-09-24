/* GroupList, ItemList, NotificationList and helpers are application-defined. */
GroupList groups = load_groups();
size_t groupIndex = 0;
while (groupIndex < groups.count) {
    ItemList items = groups.data[groupIndex];
    size_t itemIndex = 0;
    while (itemIndex < items.count) {
        process_item(items.data[itemIndex]);
        itemIndex = itemIndex + 1;
    }
    NotificationList notifications = load_notifications(groups.data[groupIndex]);
    size_t notificationIndex = 0;
    while (notificationIndex < notifications.count) {
        send_notification(notifications.data[notificationIndex]);
        notificationIndex = notificationIndex + 1;
    }
    groupIndex = groupIndex + 1;
}
show_summary();
