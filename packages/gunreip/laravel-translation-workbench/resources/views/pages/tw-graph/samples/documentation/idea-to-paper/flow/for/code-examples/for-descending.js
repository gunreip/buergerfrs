const items = loadItems();
const count = items.length;
for (let index = count - 1; index >= 0; index -= 2) {
    processItem(items[index]);
}
showSummary();
