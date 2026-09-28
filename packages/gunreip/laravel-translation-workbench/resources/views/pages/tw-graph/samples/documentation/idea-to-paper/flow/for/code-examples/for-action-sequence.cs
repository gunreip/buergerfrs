var groups = LoadGroups(); // List<List<Item>>
for (var groupIndex = 0; groupIndex < groups.Count; groupIndex++) {
    var items = groups[groupIndex];
    PrepareGroup(items);
    for (var itemIndex = 0; itemIndex < items.Count; itemIndex++) {
        ProcessItem(items[itemIndex]);
    }
    FinalizeGroup(items);
}
ShowSummary();
