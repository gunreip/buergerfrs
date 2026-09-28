List<Item> items = loadItems();
for (Item item : items) {
    try {
        processItem(item);
        recordSuccess(item);
    } catch (Exception error) {
        handleItemFailure(item, error);
    }
}
showSummary();
