var items = loadItems(); // List<Item>
int index = 0;
while (index < items.size()) {
    var item = items.get(index);
    index++;
    if (shouldSkip(item)) {
        continue;
    }
    processItem(item);
}
showSummary();
