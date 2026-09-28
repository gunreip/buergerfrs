auto items = loadItems();
for (const auto& item : items) {
    processItem(item);
}
showSummary();
