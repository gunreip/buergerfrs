List<List<Item>> groups = loadGroups();
int groupIndex = 0;
while (groupIndex < groups.size()) {
    List<Item> items = groups.get(groupIndex);
    prepareGroup(items);
    int itemIndex = 0;
    while (itemIndex < items.size()) {
        processItem(items.get(itemIndex));
        itemIndex = itemIndex + 1;
    }
    finalizeGroup(items);
    groupIndex = groupIndex + 1;
}
showSummary();
