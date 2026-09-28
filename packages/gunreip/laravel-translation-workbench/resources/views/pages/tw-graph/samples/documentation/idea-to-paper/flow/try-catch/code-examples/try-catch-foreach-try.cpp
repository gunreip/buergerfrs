auto items = loadItems();
for (const auto& item : items) {
    try {
        processItem(item);
        recordSuccess(item);
    } catch (...) {
        handleItemFailure(item);
    }
}
showSummary();
