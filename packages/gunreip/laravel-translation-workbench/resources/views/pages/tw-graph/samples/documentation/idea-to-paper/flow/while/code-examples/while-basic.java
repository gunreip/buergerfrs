Queue<Item> pending = loadPendingItems();
while (!pending.isEmpty()) {
    Item item = pending.remove();
    processItem(item);
}
showSummary();
