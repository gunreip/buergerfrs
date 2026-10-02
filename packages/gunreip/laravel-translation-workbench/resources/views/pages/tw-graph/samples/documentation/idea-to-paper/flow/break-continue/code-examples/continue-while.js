const items = loadItems();
let index = 0;
while (index < items.length) {
    const item = items[index];
    index++;
    if (shouldSkip(item)) {
        continue;
    }
    processItem(item);
}
showSummary();
