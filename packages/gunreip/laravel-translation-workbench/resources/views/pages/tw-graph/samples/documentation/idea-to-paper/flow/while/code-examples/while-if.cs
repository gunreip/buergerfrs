var items = LoadItems(); // List<Item>
var index = 0;
while (index < items.Count) {
    var item = items[index];
    if (item.Enabled) {
        ProcessItem(item);
    } else {
        RecordSkippedItem(item);
    }
    index = index + 1;
}
ShowSummary();
