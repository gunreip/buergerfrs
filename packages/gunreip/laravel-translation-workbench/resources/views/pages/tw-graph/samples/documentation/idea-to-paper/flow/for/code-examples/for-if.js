const items = loadItems();
const count = items.length;
for (let index = 0; index < count; index++) {
    const item = items[index];
    if (item.enabled) {
        processItem(item);
    } else {
        recordSkippedItem(item);
    }
}
showSummary();
