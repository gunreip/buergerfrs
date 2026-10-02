const items = loadItems();
let index = 0;
while (index < items.length) {
    const item = items[index];
    try {
        if (mayProcess(item)) {
            processItem(item);
            index++;
        } else {
            break;
        }
    } finally {
        cleanupItem(item);
    }
}
showSummary();
