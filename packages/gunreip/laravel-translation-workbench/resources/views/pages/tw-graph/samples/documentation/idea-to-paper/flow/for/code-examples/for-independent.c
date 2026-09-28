/* GroupList, ItemList, NotificationList and helpers are application-defined. */
GroupList groups = load_groups();
for (size_t groupIndex = 0; groupIndex < groups.count; groupIndex++) {
    ItemList items = groups.data[groupIndex];
    for (size_t itemIndex = 0; itemIndex < items.count; itemIndex++) {
        process_item(items.data[itemIndex]);
    }
    NotificationList notifications = load_notifications(groups.data[groupIndex]);
    for (size_t notificationIndex = 0; notificationIndex < notifications.count; notificationIndex++) {
        send_notification(notifications.data[notificationIndex]);
    }
}
show_summary();
