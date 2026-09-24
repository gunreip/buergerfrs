var items = LoadItems(); // List<Item>
var index = 0;
while (index < items.Count) {
    ProcessItem(items[index]);
    index = index + 1;
}
ShowSummary();
