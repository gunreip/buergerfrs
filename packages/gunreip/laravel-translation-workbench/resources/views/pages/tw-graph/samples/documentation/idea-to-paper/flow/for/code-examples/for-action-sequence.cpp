auto groups = loadGroups(); // std::vector<std::vector<Item>>
for (std::size_t groupIndex = 0; groupIndex < groups.size(); groupIndex++) {
    const auto& items = groups[groupIndex];
    prepareGroup(items);
    for (std::size_t itemIndex = 0; itemIndex < items.size(); itemIndex++) {
        processItem(items[itemIndex]);
    }
    finalizeGroup(items);
}
showSummary();
