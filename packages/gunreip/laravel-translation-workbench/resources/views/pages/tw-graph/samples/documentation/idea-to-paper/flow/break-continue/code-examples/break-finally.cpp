// A scope guard runs on normal scope exit and BREAK (no FINALLY keyword).
// Application type Item and cleanupItem are omitted; cleanupItem must not throw.
auto items = loadItems();
std::size_t index = 0;
while (index < items.size()) {
    const auto& item = items[index];
    struct CleanupGuard {
        const Item& item;
        ~CleanupGuard() noexcept { cleanupItem(item); }
    } cleanupGuard{item};
    if (mayProcess(item)) {
        processItem(item);
        index++;
    } else {
        break;
    }
}
showSummary();
