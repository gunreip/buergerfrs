auto queue = loadQueue();
prepareQueue(queue);
while (hasNext(queue)) {
    auto item = nextItem(queue);
    {
        struct CleanupGuard {
            const Item& item;
            ~CleanupGuard() noexcept { cleanupItem(item); }
        } cleanupGuard{item};
        try {
            processItem(item);
            recordSuccess(item);
        } catch (...) {
            handleItemFailure(item);
        }
    }
}
showSummary();
