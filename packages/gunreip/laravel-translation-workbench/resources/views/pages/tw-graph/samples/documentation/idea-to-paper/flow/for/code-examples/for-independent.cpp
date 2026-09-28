auto groups = loadGroups(); // std::vector<std::vector<Item>>
for (std::size_t groupIndex = 0; groupIndex < groups.size(); groupIndex++) {
    const auto& items = groups[groupIndex];
    for (std::size_t itemIndex = 0; itemIndex < items.size(); itemIndex++) {
        processItem(items[itemIndex]);
    }
    auto notifications = loadNotifications(groups[groupIndex]);
    for (std::size_t notificationIndex = 0; notificationIndex < notifications.size(); notificationIndex++) {
        sendNotification(notifications[notificationIndex]);
    }
}
showSummary();
