List<Item> items = loadItems();
int index = 0;
while (index < items.size()) {
    processItem(items.get(index));
    index = index + 1;
}
showSummary();
