const pending = loadPendingItems();
while (pending.length > 0) {
    const item = pending.shift();
    processItem(item);
}
showSummary();
