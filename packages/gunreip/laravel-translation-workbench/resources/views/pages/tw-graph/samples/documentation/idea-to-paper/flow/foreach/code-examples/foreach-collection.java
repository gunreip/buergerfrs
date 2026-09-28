List<Item> items = loadItems();
for (Item item : items) {
    processItem(item);
}
showSummary();
