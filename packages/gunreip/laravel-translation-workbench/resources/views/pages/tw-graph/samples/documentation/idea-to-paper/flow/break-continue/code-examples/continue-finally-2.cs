var items = LoadItems(); // List<Item>
var index = 0;
while (index < items.Count) {
    var item = items[index];
    index++;
    try {
        if (ShouldSkip(item)) {
            continue;
        }
        ProcessItem(item);
    } finally {
        CleanupItem(item);
    }
}
ShowSummary();
