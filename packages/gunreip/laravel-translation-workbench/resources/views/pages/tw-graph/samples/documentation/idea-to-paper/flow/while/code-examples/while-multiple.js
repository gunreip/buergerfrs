const items = loadItems();
let index = 0;
while (index < items.length) {
    processItem(items[index]);
    index = index + 1;
}
showSummary();
