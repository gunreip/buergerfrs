var groups = LoadGroups(); // List<List<Item>>
var groupIndex = 0;
while (groupIndex < groups.Count) {
    var items = groups[groupIndex];
    var itemIndex = 0;
    while (itemIndex < items.Count) {
        var item = items[itemIndex];
        itemIndex++;
        if (ShouldSkip(item)) {
            continue; // Only the inner WHILE.
        }
        ProcessItem(item);
    }
    groupIndex++;
}
ShowSummary();
