var groups = LoadGroups(); // List<List<Item>>
var groupIndex = 0;
while (groupIndex < groups.Count) {
    var items = groups[groupIndex];
    PrepareGroup(items);
    var itemIndex = 0;
    while (itemIndex < items.Count) {
        ProcessItem(items[itemIndex]);
        itemIndex = itemIndex + 1;
    }
    FinalizeGroup(items);
    groupIndex = groupIndex + 1;
}
ShowSummary();
