Queue<Item> queue = loadQueue();
prepareQueue(queue);
while (hasNext(queue)) {
    Item item = nextItem(queue);
    try {
        processItem(item);
        recordSuccess(item);
    } catch (Exception error) {
        handleItemFailure(item, error);
    } finally {
        cleanupItem(item);
    }
}
showSummary();
