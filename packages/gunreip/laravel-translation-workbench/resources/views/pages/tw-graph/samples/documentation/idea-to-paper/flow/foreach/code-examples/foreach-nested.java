List<List<Item>> groups = loadGroups();
for (List<Item> group : groups) {
    for (Item item : group) {
        processItem(item);
        recordItemResult(item);
    }
    finalizeGroup(group);
}
showSummary();
