auto items = loadItems();
try {
    for (const auto& item : items) {
        processItem(item);
        recordSuccess(item);
    }
} catch (...) {
    handleFailure();
}
continueProcess();
