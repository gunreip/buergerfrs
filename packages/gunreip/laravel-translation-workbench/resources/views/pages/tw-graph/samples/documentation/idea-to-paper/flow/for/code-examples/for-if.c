// loadItems supplies an array and writes its element count.
size_t count = 0;
const Item *items = loadItems(&count);
for (size_t index = 0; index < count; index++) {
    const Item item = items[index];
    if (item.enabled) {
        processItem(item);
    } else {
        recordSkippedItem(item);
    }
}
showSummary();
