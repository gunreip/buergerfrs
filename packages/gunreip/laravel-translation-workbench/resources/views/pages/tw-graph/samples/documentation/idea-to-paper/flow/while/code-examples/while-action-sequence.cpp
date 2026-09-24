auto groups = loadGroups(); // std::vector<std::vector<Item>>
std::size_t groupIndex = 0;
while (groupIndex < groups.size()) {
    const auto& items = groups[groupIndex];
    prepareGroup(items);
    std::size_t itemIndex = 0;
    while (itemIndex < items.size()) {
        processItem(items[itemIndex]);
        itemIndex = itemIndex + 1;
    }
    finalizeGroup(items);
    groupIndex = groupIndex + 1;
}
showSummary();
