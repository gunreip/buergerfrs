var items = loadItems(); // Returns a list.
int count = items.size();
for (int index = 0; index < count; index++) {
    var item = items.get(index);
    if (item.isEnabled()) {
        processItem(item);
    } else {
        recordSkippedItem(item);
    }
}
showSummary();
