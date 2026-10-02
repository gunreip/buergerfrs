const items = loadItems();
for (let index = 0; index < items.length; index++) {
    const item = items[index];
    if (shouldSkip(item)) {
        continue;
    }
    processItem(item);
}
showSummary();
