var items = LoadItems(); // Returns an array.
var count = items.Length;
for (int index = 0; index < count; index++) {
    ProcessItem(items[index]);
}
ShowSummary();
