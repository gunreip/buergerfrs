var groups = loadGroups(); // List<List<Item>>
int groupIndex = 0;
while (groupIndex < groups.size()) {
    var items = groups.get(groupIndex);
    int itemIndex = 0;
    while (itemIndex < items.size()) {
        var item = items.get(itemIndex);
        itemIndex++;
        if (shouldSkip(item)) {
            continue; // Only the inner WHILE.
        }
        processItem(item);
    }
    groupIndex++;
}
showSummary();
