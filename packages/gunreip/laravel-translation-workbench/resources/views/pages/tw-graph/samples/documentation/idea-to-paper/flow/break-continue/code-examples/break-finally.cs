var items = LoadItems(); // List<Item>
var index = 0;
while (index < items.Count) {
    var item = items[index];
    try {
        if (MayProcess(item)) {
            ProcessItem(item);
            index++;
        } else {
            break;
        }
    } finally {
        CleanupItem(item);
    }
}
ShowSummary();
