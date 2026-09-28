const items = loadItems();
try {
    for (const item of items) {
        processItem(item);
        recordSuccess(item);
    }
} catch (error) {
    handleFailure(error);
}
continueProcess();
