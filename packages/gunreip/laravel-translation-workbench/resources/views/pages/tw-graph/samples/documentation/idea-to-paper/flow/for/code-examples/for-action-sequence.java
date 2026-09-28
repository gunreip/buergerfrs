List<List<Item>> groups = loadGroups();
for (int groupIndex = 0; groupIndex < groups.size(); groupIndex++) {
    List<Item> items = groups.get(groupIndex);
    prepareGroup(items);
    for (int itemIndex = 0; itemIndex < items.size(); itemIndex++) {
        processItem(items.get(itemIndex));
    }
    finalizeGroup(items);
}
showSummary();
