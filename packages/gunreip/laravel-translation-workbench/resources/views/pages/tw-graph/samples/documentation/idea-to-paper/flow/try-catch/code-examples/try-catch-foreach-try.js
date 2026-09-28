const items = loadItems();
for (const item of items) {
    try {
        processItem(item);
        recordSuccess(item);
    } catch (error) {
        handleItemFailure(item, error);
    }
}
showSummary();
