const queue = loadQueue();
prepareQueue(queue);
while (hasNext(queue)) {
    const item = nextItem(queue);
    try {
        processItem(item);
        recordSuccess(item);
    } catch (error) {
        handleItemFailure(item, error);
    } finally {
        cleanupItem(item);
    }
}
showSummary();
