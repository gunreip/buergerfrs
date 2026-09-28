// loadItems supplies an array and writes its element count.
size_t count = 0;
const Item *items = loadItems(&count);
for (size_t index = 0; index < count; index++) {
    processItem(items[index]);
}
showSummary();
