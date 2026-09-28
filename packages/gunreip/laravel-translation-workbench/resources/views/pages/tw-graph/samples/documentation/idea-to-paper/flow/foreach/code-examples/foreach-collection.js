const items = loadItems();
for (const item of items) {
    processItem(item);
}
showSummary();
