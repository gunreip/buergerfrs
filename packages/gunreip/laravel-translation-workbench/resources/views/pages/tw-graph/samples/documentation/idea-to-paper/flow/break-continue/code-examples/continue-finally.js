const items = loadItems();
let index = 0;
while (index < items.length) {
    const item = items[index];
    index++;
    try {
        if (shouldSkip(item)) {
            continue;
        }
        processItem(item);
    } finally {
        cleanupItem(item);
    }
}
showSummary();
