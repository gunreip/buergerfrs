var items = loadItems(); // List<Item>
for (int index = 0; index < items.size(); index++) {
    var item = items.get(index);
    if (shouldSkip(item)) {
        continue;
    }
    processItem(item);
}
showSummary();
