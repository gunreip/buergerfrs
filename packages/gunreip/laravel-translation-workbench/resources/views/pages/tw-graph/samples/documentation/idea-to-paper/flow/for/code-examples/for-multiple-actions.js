const items = loadItems();
const count = items.length;
for (let index = 0; index < count; index++) {
    processItem(items[index]);
    recordResult(items[index]);
}
showSummary();
