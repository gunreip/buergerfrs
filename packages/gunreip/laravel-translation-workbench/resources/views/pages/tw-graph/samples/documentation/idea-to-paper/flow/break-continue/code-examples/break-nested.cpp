auto groups = loadGroups();
std::size_t groupIndex = 0;
while (groupIndex < groups.size()) {
    const auto& items = groups[groupIndex];
    std::size_t itemIndex = 0;
    while (itemIndex < items.size()) {
        const auto& item = items[itemIndex];
        itemIndex++;
        if (!mayProcess(item)) {
            break; // Only the inner WHILE.
        }
        processItem(item);
    }
    groupIndex++;
}
showSummary();
