var items = LoadItems(); // List<Item>
var index = 0;
while (index < items.Count) {
    var item = items[index];
    index++;
    if (ShouldSkip(item)) {
        continue;
    }
    ProcessItem(item);
}
ShowSummary();
