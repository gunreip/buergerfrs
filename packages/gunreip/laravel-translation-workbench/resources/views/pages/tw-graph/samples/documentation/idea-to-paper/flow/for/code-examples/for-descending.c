// loadItems supplies an array and a count representable as ptrdiff_t.
ptrdiff_t count = 0;
const Item *items = loadItems(&count);
for (ptrdiff_t index = count - 1; index >= 0; index -= 2) {
    processItem(items[index]);
}
showSummary();
