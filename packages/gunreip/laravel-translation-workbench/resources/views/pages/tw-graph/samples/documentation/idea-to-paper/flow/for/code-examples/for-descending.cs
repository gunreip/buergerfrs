var items = LoadItems(); // Returns an array.
var count = items.Length;
for (int index = count - 1; index >= 0; index -= 2) {
    ProcessItem(items[index]);
}
ShowSummary();
