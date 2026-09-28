var items = LoadItems(); // Returns an array.
var count = items.Length;
for (int index = 0; index < count; index++) {
    var item = items[index];
    if (item.Enabled) {
        ProcessItem(item);
    } else {
        RecordSkippedItem(item);
    }
}
ShowSummary();
