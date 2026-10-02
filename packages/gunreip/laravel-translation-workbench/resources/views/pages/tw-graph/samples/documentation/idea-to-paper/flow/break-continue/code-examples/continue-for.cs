var items = LoadItems(); // List<Item>
for (var index = 0; index < items.Count; index++) {
    var item = items[index];
    if (ShouldSkip(item)) {
        continue;
    }
    ProcessItem(item);
}
ShowSummary();
