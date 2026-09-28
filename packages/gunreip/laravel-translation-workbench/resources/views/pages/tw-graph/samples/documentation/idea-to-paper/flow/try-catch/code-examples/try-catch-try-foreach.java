List<Item> items = loadItems();
try {
    for (Item item : items) {
        processItem(item);
        recordSuccess(item);
    }
} catch (Exception error) {
    handleFailure(error);
}
continueProcess();
