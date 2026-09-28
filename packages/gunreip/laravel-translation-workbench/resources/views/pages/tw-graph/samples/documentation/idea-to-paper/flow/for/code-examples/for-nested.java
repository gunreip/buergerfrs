List<List<Item>> groups = loadGroups();
for (int groupIndex = 0; groupIndex < groups.size(); groupIndex++) {
    List<Item> items = groups.get(groupIndex);
    for (int itemIndex = 0; itemIndex < items.size(); itemIndex++) {
        processItem(items.get(itemIndex));
    }
}
showSummary();
