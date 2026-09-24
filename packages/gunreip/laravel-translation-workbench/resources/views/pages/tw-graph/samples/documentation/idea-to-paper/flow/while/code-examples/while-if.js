const items = loadItems();
let index = 0;
while (index < items.length) {
    const item = items[index];
    if (item.enabled) {
        processItem(item);
    } else {
        recordSkippedItem(item);
    }
    index = index + 1;
}
showSummary();
