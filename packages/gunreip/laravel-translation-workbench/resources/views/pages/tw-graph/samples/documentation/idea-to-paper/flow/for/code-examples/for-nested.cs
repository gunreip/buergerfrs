var groups = LoadGroups(); // List<List<Item>>
for (var groupIndex = 0; groupIndex < groups.Count; groupIndex++) {
    var items = groups[groupIndex];
    for (var itemIndex = 0; itemIndex < items.Count; itemIndex++) {
        ProcessItem(items[itemIndex]);
    }
}
ShowSummary();
