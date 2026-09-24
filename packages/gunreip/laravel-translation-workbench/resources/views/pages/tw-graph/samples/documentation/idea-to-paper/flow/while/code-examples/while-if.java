List<Item> items = loadItems();
int index = 0;
while (index < items.size()) {
    Item item = items.get(index);
    if (item.isEnabled()) {
        processItem(item);
    } else {
        recordSkippedItem(item);
    }
    index = index + 1;
}
showSummary();
